const express = require("express");

const router = express.Router();

const materialUsageController = require("../controllers/materialUsageController");

const {
    requireAuth,
    requireRole
} = require("../middleware/authMiddleware");

router.get(
    "/material-usage",
    requireAuth,
    requireRole(
        "Administrator",
        "Project Manager",
        "Site Staff"
    ),
    materialUsageController.index
);

router.get(
    "/material-usage/add",
    requireAuth,
    requireRole(
        "Administrator",
        "Site Staff"
    ),
    materialUsageController.showAddForm
);

router.post(
    "/material-usage/add",
    requireAuth,
    requireRole(
        "Administrator",
        "Site Staff"
    ),
    materialUsageController.create
);

router.get(
    "/material-usage/edit/:id",
    requireAuth,
    requireRole(
        "Administrator",
        "Site Staff"
    ),
    materialUsageController.showEditForm
);

router.post(
    "/material-usage/edit/:id",
    requireAuth,
    requireRole(
        "Administrator",
        "Site Staff"
    ),
    materialUsageController.update
);

router.get(
    "/material-usage/delete/:id",
    requireAuth,
    requireRole(
        "Administrator",
        "Site Staff"
    ),
    materialUsageController.delete
);

module.exports = router;