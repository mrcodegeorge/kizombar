<?php

class PushSubscription {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function save($user_id, $endpoint, $p256dh, $auth) {
        // Remove old subscriptions for this user+endpoint combo
        $del = $this->conn->prepare("DELETE FROM push_subscriptions WHERE user_id = ? AND endpoint = ?");
        $del->execute([$user_id, $endpoint]);

        $stmt = $this->conn->prepare(
            "INSERT INTO push_subscriptions (user_id, endpoint, p256dh, auth) VALUES (?, ?, ?, ?)"
        );
        return $stmt->execute([$user_id, $endpoint, $p256dh, $auth]);
    }

    public function getAllForUser($user_id) {
        $stmt = $this->conn->prepare("SELECT * FROM push_subscriptions WHERE user_id = ?");
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAll() {
        $stmt = $this->conn->query("SELECT ps.*, u.name FROM push_subscriptions ps JOIN users u ON ps.user_id = u.id");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
