const express = require("express");

const router = express.Router();

const auditController = require("../controllers/auditController");

const {
    requireAuth,
    requireRole
} = require("../middleware/authMiddleware");

router.get(
    "/audit-logs",
    requireAuth,
    requireRole("Administrator"),
    auditController.index
);

module.exports = router;