<?php
require_once __DIR__ . '/../settings/db_class.php';

if (!class_exists('Category')) {
    class Category
    {
        private $db;

        public function __construct()
        {
            $this->db = new db_connection();
        }

        // add category; tries to use user_id column if it exists
        public function addCat($cat_name, $user_id = null)
        {
            $conn = $this->db->db_conn();
            if (!$conn) return false;

            // detect if user_id column exists
            $hasUserCol = $this->hasUserColumn($conn);

            if ($hasUserCol && $user_id !== null) {
                $stmt = mysqli_prepare($conn, "INSERT INTO categories (cat_name, user_id) VALUES (?, ?)");
                mysqli_stmt_bind_param($stmt, 'si', $cat_name, $user_id);
            } else {
                $stmt = mysqli_prepare($conn, "INSERT INTO categories (cat_name) VALUES (?)");
                mysqli_stmt_bind_param($stmt, 's', $cat_name);
            }

            $ok = mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            return $ok;
        }

        public function updateCat($cat_id, $cat_name)
        {
            $conn = $this->db->db_conn();
            if (!$conn) return false;
            $stmt = mysqli_prepare($conn, "UPDATE categories SET cat_name = ? WHERE cat_id = ?");
            mysqli_stmt_bind_param($stmt, 'si', $cat_name, $cat_id);
            $ok = mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            return $ok;
        }

        public function deleteCat($cat_id)
        {
            $conn = $this->db->db_conn();
            if (!$conn) return false;
            $stmt = mysqli_prepare($conn, "DELETE FROM categories WHERE cat_id = ?");
            mysqli_stmt_bind_param($stmt, 'i', $cat_id);
            $ok = mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            return $ok;
        }

        // if a user_id column exists, return categories for that user; otherwise return all categories
        public function getCat($user_id = null)
        {
            $conn = $this->db->db_conn();
            if (!$conn) return [];

            $hasUserCol = $this->hasUserColumn($conn);

            if ($hasUserCol && $user_id !== null) {
                $stmt = mysqli_prepare($conn, "SELECT cat_id, cat_name FROM categories WHERE user_id = ? ORDER BY cat_id DESC");
                mysqli_stmt_bind_param($stmt, 'i', $user_id);
                mysqli_stmt_execute($stmt);
                $res = mysqli_stmt_get_result($stmt);
                $rows = mysqli_fetch_all($res, MYSQLI_ASSOC);
                mysqli_stmt_close($stmt);
                return $rows ?: [];
            }

            // fallback: return all categories
            $sql = "SELECT cat_id, cat_name FROM categories ORDER BY cat_id DESC";
            $rows = $this->db->db_fetch_all($sql);
            return $rows ?: [];
        }

        public function getAllCat()
        {
            return $this->getCat();
        }

        private function hasUserColumn($conn)
        {
            $res = mysqli_query($conn, "SHOW COLUMNS FROM categories LIKE 'user_id'");
            if ($res === false) return false;
            return mysqli_num_rows($res) > 0;
        }
    }
}
