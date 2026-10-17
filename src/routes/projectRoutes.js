const express = require("express");

const router = express.Router();

const projectController = require("../controllers/projectController");

const {
    requireAuth,
    requireRole
} = require("../middleware/authMiddleware");

router.get(
    "/projects",
    requireAuth,
    requireRole(
        "Administrator",
        "Project Manager",
        "Site Staff"
    ),
    projectController.index
);

router.get(
    "/projects/add",
    requireAuth,
    requireRole(
        "Administrator",
        "Project Manager"
    ),
    projectController.showAddForm
);

router.post(
    "/projects/add",
    requireAuth,
    requireRole(
        "Administrator",
        "Project Manager"
    ),
    projectController.create
);

router.get(
    "/projects/edit/:id",
    requireAuth,
    requireRole(
        "Administrator",
        "Project Manager"
    ),
    projectController.showEditForm
);

router.post(
    "/projects/edit/:id",
    requireAuth,
    requireRole(
        "Administrator",
        "Project Manager"
    ),
    projectController.update
);

router.get(
    "/projects/delete/:id",
    requireAuth,
    requireRole(
        "Administrator",
        "Project Manager"
    ),
    projectController.delete
);

module.exports = router;