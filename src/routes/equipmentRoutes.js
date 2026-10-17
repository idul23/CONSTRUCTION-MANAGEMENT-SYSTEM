const express = require("express");

const router = express.Router();

const equipmentController = require("../controllers/equipmentController");

const {
    requireAuth,
    requireRole
} = require("../middleware/authMiddleware");

router.get(
    "/equipment",
    requireAuth,
    requireRole(
        "Administrator",
        "Project Manager",
        "Site Staff"
    ),
    equipmentController.index
);

router.get(
    "/equipment/add",
    requireAuth,
    requireRole(
        "Administrator",
        "Project Manager"
    ),
    equipmentController.showAddForm
);

router.post(
    "/equipment/add",
    requireAuth,
    requireRole(
        "Administrator",
        "Project Manager"
    ),
    equipmentController.create
);

router.get(
    "/equipment/edit/:id",
    requireAuth,
    requireRole(
        "Administrator",
        "Project Manager"
    ),
    equipmentController.showEditForm
);

router.post(
    "/equipment/edit/:id",
    requireAuth,
    requireRole(
        "Administrator",
        "Project Manager"
    ),
    equipmentController.update
);

router.get(
    "/equipment/delete/:id",
    requireAuth,
    requireRole(
        "Administrator",
        "Project Manager"
    ),
    equipmentController.delete
);

module.exports = router;