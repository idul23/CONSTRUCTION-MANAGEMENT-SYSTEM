const express = require("express");

const router = express.Router();

const taskController = require("../controllers/taskController");

const {
    requireAuth,
    requireRole
} = require("../middleware/authMiddleware");

router.get(
    "/tasks",
    requireAuth,
    requireRole(
        "Administrator",
        "Project Manager",
        "Site Staff"
    ),
    taskController.index
);

router.get(
    "/tasks/add",
    requireAuth,
    requireRole(
        "Administrator",
        "Project Manager"
    ),
    taskController.showAddForm
);

router.post(
    "/tasks/add",
    requireAuth,
    requireRole(
        "Administrator",
        "Project Manager"
    ),
    taskController.create
);

router.get(
    "/tasks/edit/:id",
    requireAuth,
    requireRole(
        "Administrator",
        "Project Manager",
        "Site Staff"
    ),
    taskController.showEditForm
);

router.post(
    "/tasks/edit/:id",
    requireAuth,
    requireRole(
        "Administrator",
        "Project Manager",
        "Site Staff"
    ),
    taskController.update
);

router.get(
    "/tasks/delete/:id",
    requireAuth,
    requireRole(
        "Administrator",
        "Project Manager"
    ),
    taskController.delete
);

module.exports = router;