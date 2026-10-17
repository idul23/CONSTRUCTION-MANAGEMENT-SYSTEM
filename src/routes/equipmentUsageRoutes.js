const express = require("express");

const router = express.Router();

const equipmentUsageController = require("../controllers/equipmentUsageController");

const {
    requireAuth,
    requireRole
} = require("../middleware/authMiddleware");

router.get(
    "/equipment-usage",
    requireAuth,
    requireRole(
        "Administrator",
        "Project Manager",
        "Site Staff"
    ),
    equipmentUsageController.index
);

router.get(
    "/equipment-usage/add",
    requireAuth,
    requireRole(
        "Administrator",
        "Site Staff"
    ),
    equipmentUsageController.showAddForm
);

router.post(
    "/equipment-usage/add",
    requireAuth,
    requireRole(
        "Administrator",
        "Site Staff"
    ),
    equipmentUsageController.create
);

router.get(
    "/equipment-usage/edit/:id",
    requireAuth,
    requireRole(
        "Administrator",
        "Site Staff"
    ),
    equipmentUsageController.showEditForm
);

router.post(
    "/equipment-usage/edit/:id",
    requireAuth,
    requireRole(
        "Administrator",
        "Site Staff"
    ),
    equipmentUsageController.update
);

router.get(
    "/equipment-usage/delete/:id",
    requireAuth,
    requireRole(
        "Administrator",
        "Site Staff"
    ),
    equipmentUsageController.delete
);

module.exports = router;