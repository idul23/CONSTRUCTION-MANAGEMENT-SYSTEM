const express = require("express");

const router = express.Router();

const attendanceController = require("../controllers/attendanceController");

const {
    requireAuth,
    requireRole
} = require("../middleware/authMiddleware");

router.get(
    "/attendance",
    requireAuth,
    requireRole(
        "Administrator",
        "Project Manager",
        "Site Staff"
    ),
    attendanceController.index
);

router.get(
    "/attendance/add",
    requireAuth,
    requireRole(
        "Administrator",
        "Site Staff"
    ),
    attendanceController.showAddForm
);

router.post(
    "/attendance/add",
    requireAuth,
    requireRole(
        "Administrator",
        "Site Staff"
    ),
    attendanceController.create
);

router.get(
    "/attendance/edit/:id",
    requireAuth,
    requireRole(
        "Administrator",
        "Site Staff"
    ),
    attendanceController.showEditForm
);

router.post(
    "/attendance/edit/:id",
    requireAuth,
    requireRole(
        "Administrator",
        "Site Staff"
    ),
    attendanceController.update
);

router.get(
    "/attendance/delete/:id",
    requireAuth,
    requireRole(
        "Administrator",
        "Site Staff"
    ),
    attendanceController.delete
);

module.exports = router;