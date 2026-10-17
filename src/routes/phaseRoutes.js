const express = require("express");

const router = express.Router();

const phaseController = require("../controllers/phaseController");

const {
    requireAuth,
    requireRole
} = require("../middleware/authMiddleware");

router.get(
    "/phases",
    requireAuth,
    requireRole(
        "Administrator",
        "Project Manager",
        "Site Staff"
    ),
    phaseController.index
);

router.get(
    "/phases/add",
    requireAuth,
    requireRole(
        "Administrator",
        "Project Manager"
    ),
    phaseController.showAddForm
);

router.post(
    "/phases/add",
    requireAuth,
    requireRole(
        "Administrator",
        "Project Manager"
    ),
    phaseController.create
);

router.get(
    "/phases/edit/:id",
    requireAuth,
    requireRole(
        "Administrator",
        "Project Manager"
    ),
    phaseController.showEditForm
);

router.post(
    "/phases/edit/:id",
    requireAuth,
    requireRole(
        "Administrator",
        "Project Manager"
    ),
    phaseController.update
);

router.get(
    "/phases/delete/:id",
    requireAuth,
    requireRole(
        "Administrator",
        "Project Manager"
    ),
    phaseController.delete
);

module.exports = router;