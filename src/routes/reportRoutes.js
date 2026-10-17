const express = require("express");

const router = express.Router();

const reportController = require("../controllers/reportController");

const {
    requireAuth,
    requireRole
} = require("../middleware/authMiddleware");

router.get(
    "/reports",
    requireAuth,
    requireRole(
        "Administrator",
        "Project Manager",
        "Site Staff"
    ),
    reportController.index
);

module.exports = router;