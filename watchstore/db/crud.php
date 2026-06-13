<?php
class Crud {
    private $db;

    function __construct($pdo) {
        $this->db = $pdo;
    }

    // ==================== USERS ====================

    public function registerUser($full_name, $email, $phone, $address, $password) {
    try {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare("INSERT INTO users (full_name, email, phone, address, password) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$full_name, $email, $phone, $address, $hashed]);
        return true;
    } catch (PDOException $e) {
        echo $e->getMessage();
        return false;
    }
}

    public function emailExists($email) {
        try {
            $stmt = $this->db->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            echo $e->getMessage();
            return false;
        }
    }

    public function loginUser($email) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            echo $e->getMessage();
            return false;
        }
    }

    public function getAllUsers() {
        try {
            return $this->db->query("SELECT * FROM users ORDER BY created_at DESC")->fetchAll();
        } catch (PDOException $e) {
            echo $e->getMessage();
            return false;
        }
    }

    public function deleteUser($id) {
    try {
        // Delete related order items first
        $orders = $this->db->prepare("SELECT id FROM orders WHERE user_id = ?");
        $orders->execute([$id]);
        $orders = $orders->fetchAll();
        foreach ($orders as $order) {
            $this->db->prepare("DELETE FROM order_items WHERE order_id = ?")->execute([$order['id']]);
        }
        // Delete related orders
        $this->db->prepare("DELETE FROM orders WHERE user_id = ?")->execute([$id]);
        // Delete related cart
        $this->db->prepare("DELETE FROM cart WHERE user_id = ?")->execute([$id]);
        // Now delete user
        $stmt = $this->db->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return true;
    } catch (PDOException $e) {
        echo $e->getMessage();
        return false;
    }
}

    // ==================== PRODUCTS ====================

    public function getAllProducts() {
        try {
            return $this->db->query("SELECT p.*, c.name as category_name FROM products p JOIN categories c ON p.category_id = c.id")->fetchAll();
        } catch (PDOException $e) {
            echo $e->getMessage();
            return false;
        }
    }

    public function getProductsByCategory($category_id) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM products WHERE category_id = ?");
            $stmt->execute([$category_id]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            echo $e->getMessage();
            return false;
        }
    }

    public function getProductById($id) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM products WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            echo $e->getMessage();
            return false;
        }
    }

    public function addProduct($category_id, $name, $price, $image, $description) {
        try {
            $stmt = $this->db->prepare("INSERT INTO products (category_id, name, price, image, description) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$category_id, $name, $price, $image, $description]);
            return true;
        } catch (PDOException $e) {
            echo $e->getMessage();
            return false;
        }
    }

    public function updateProduct($id, $name, $price, $category_id, $image, $description) {
        try {
            $stmt = $this->db->prepare("UPDATE products SET name=?, price=?, category_id=?, image=?, description=? WHERE id=?");
            $stmt->execute([$name, $price, $category_id, $image, $description, $id]);
            return true;
        } catch (PDOException $e) {
            echo $e->getMessage();
            return false;
        }
    }

    public function deleteProduct($id) {
    try {
        // Delete related order items first
        $this->db->prepare("DELETE FROM order_items WHERE product_id = ?")->execute([$id]);
        // Delete related cart items
        $this->db->prepare("DELETE FROM cart WHERE product_id = ?")->execute([$id]);
        // Now delete product
        $stmt = $this->db->prepare("DELETE FROM products WHERE id = ?");
        $stmt->execute([$id]);
        return true;
    } catch (PDOException $e) {
        echo $e->getMessage();
        return false;
    }
}

    // ==================== CATEGORIES ====================

    public function getAllCategories() {
        try {
            return $this->db->query("SELECT * FROM categories")->fetchAll();
        } catch (PDOException $e) {
            echo $e->getMessage();
            return false;
        }
    }

    // ==================== ORDERS ====================

    public function placeOrder($user_id, $total_amount) {
        try {
            $stmt = $this->db->prepare("INSERT INTO orders (user_id, total_amount, status) VALUES (?, ?, 'pending')");
            $stmt->execute([$user_id, $total_amount]);
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            echo $e->getMessage();
            return false;
        }
    }

    public function addOrderItem($order_id, $product_id, $quantity, $price) {
        try {
            $stmt = $this->db->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
            $stmt->execute([$order_id, $product_id, $quantity, $price]);
            return true;
        } catch (PDOException $e) {
            echo $e->getMessage();
            return false;
        }
    }

    public function getUserOrders($user_id) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
            $stmt->execute([$user_id]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            echo $e->getMessage();
            return false;
        }
    }

    public function getOrderItems($order_id) {
        try {
            $stmt = $this->db->prepare("SELECT oi.*, p.name, p.image FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?");
            $stmt->execute([$order_id]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            echo $e->getMessage();
            return false;
        }
    }

    public function getAllOrders() {
        try {
            return $this->db->query("SELECT o.*, u.full_name, u.email FROM orders o JOIN users u ON o.user_id = u.id ORDER BY o.created_at DESC")->fetchAll();
        } catch (PDOException $e) {
            echo $e->getMessage();
            return false;
        }
    }

    public function updateOrderStatus($order_id, $status) {
        try {
            $stmt = $this->db->prepare("UPDATE orders SET status = ? WHERE id = ?");
            $stmt->execute([$status, $order_id]);
            return true;
        } catch (PDOException $e) {
            echo $e->getMessage();
            return false;
        }
    }

    public function getProductByName($name) {
        try {
            $stmt = $this->db->prepare("SELECT id FROM products WHERE name = ?");
            $stmt->execute([$name]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            echo $e->getMessage();
            return false;
        }
    }

    // ==================== ADMIN ====================

    public function loginAdmin($username) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM admin WHERE username = ?");
            $stmt->execute([$username]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            echo $e->getMessage();
            return false;
        }
    }

    // ==================== STATS ====================

    public function countUsers() {
        return $this->db->query("SELECT COUNT(*) FROM users")->fetchColumn();
    }

    public function countProducts() {
        return $this->db->query("SELECT COUNT(*) FROM products")->fetchColumn();
    }

    public function countOrders() {
        return $this->db->query("SELECT COUNT(*) FROM orders")->fetchColumn();
    }
}
?>