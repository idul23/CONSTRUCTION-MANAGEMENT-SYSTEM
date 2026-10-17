const express = require("express");

const router = express.Router();

const assignmentController = require("../controllers/assignmentController");

const {
    requireAuth,
    requireRole
} = require("../middleware/authMiddleware");

router.get(
    "/assignments",
    requireAuth,
    requireRole(
        "Administrator",
        "Project Manager",
        "Site Staff"
    ),
    assignmentController.index
);

router.get(
    "/assignments/add",
    requireAuth,
    requireRole(
        "Administrator",
        "Project Manager",
        "Site Staff"
    ),
    assignmentController.showAddForm
);

router.post(
    "/assignments/add",
    requireAuth,
    requireRole(
        "Administrator",
        "Project Manager",
        "Site Staff"
    ),
    assignmentController.create
);

router.get(
    "/assignments/edit/:id",
    requireAuth,
    requireRole(
        "Administrator",
        "Project Manager",
        "Site Staff"
    ),
    assignmentController.showEditForm
);

router.post(
    "/assignments/edit/:id",
    requireAuth,
    requireRole(
        "Administrator",
        "Project Manager",
        "Site Staff"
    ),
    assignmentController.update
);

router.get(
    "/assignments/delete/:id",
    requireAuth,
    requireRole(
        "Administrator",
        "Project Manager"
    ),
    assignmentController.delete
);

module.exports = router;