const express = require("express");

const router = express.Router();

const materialController = require("../controllers/materialController");

const {
    requireAuth,
    requireRole
} = require("../middleware/authMiddleware");

router.get(
    "/materials",
    requireAuth,
    requireRole(
        "Administrator",
        "Project Manager",
        "Site Staff"
    ),
    materialController.index
);

router.get(
    "/materials/add",
    requireAuth,
    requireRole(
        "Administrator",
        "Project Manager"
    ),
    materialController.showAddForm
);

router.post(
    "/materials/add",
    requireAuth,
    requireRole(
        "Administrator",
        "Project Manager"
    ),
    materialController.create
);

router.get(
    "/materials/edit/:id",
    requireAuth,
    requireRole(
        "Administrator",
        "Project Manager"
    ),
    materialController.showEditForm
);

router.post(
    "/materials/edit/:id",
    requireAuth,
    requireRole(
        "Administrator",
        "Project Manager"
    ),
    materialController.update
);

router.get(
    "/materials/delete/:id",
    requireAuth,
    requireRole(
        "Administrator",
        "Project Manager"
    ),
    materialController.delete
);

module.exports = router;