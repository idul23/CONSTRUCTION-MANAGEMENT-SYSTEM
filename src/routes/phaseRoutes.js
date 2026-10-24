
const express = require("express");

const router = express.Router();

const phaseController = require("../controllers/phaseController");

const {
    requireAuth,
    requireRole
} = require("../middleware/authMiddleware");

const viewRoles = requireRole(
    "Administrator",
    "Project Manager",
    "Site Staff"
);

const manageRoles = requireRole(
    "Administrator",
    "Project Manager"
);

// View project phases
router.get(
    ["/project-phases", "/phases"],
    requireAuth,
    viewRoles,
    phaseController.index
);

// Show add phase form
router.get(
    ["/project-phases/add", "/phases/add"],
    requireAuth,
    manageRoles,
    phaseController.showAddForm
);

// Create a phase
router.post(
    ["/project-phases/add", "/phases/add"],
    requireAuth,
    manageRoles,
    phaseController.create
);

// Show edit phase form
router.get(
    ["/project-phases/edit/:id", "/phases/edit/:id"],
    requireAuth,
    manageRoles,
    phaseController.showEditForm
);

// Update a phase
router.post(
    ["/project-phases/edit/:id", "/phases/edit/:id"],
    requireAuth,
    manageRoles,
    phaseController.update
);

// Delete a phase
router.get(
    ["/project-phases/delete/:id", "/phases/delete/:id"],
    requireAuth,
    manageRoles,
    phaseController.delete
);

module.exports = router;
