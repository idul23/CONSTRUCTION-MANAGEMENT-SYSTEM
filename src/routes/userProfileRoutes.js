const express = require("express");

const router = express.Router();

const userProfileController = require("../controllers/userProfileController");

const {
    requireAuth,
    requireRole
} = require("../middleware/authMiddleware");

router.get(
    "/user-profiles",
    requireAuth,
    requireRole(
        "Administrator",
        "Project Manager",
        "Site Staff"
    ),
    userProfileController.index
);

router.get(
    "/user-profiles/add",
    requireAuth,
    requireRole(
        "Administrator",
        "Project Manager"
    ),
    userProfileController.showAddForm
);

router.post(
    "/user-profiles/add",
    requireAuth,
    requireRole(
        "Administrator",
        "Project Manager"
    ),
    userProfileController.create
);

router.get(
    "/user-profiles/edit/:id",
    requireAuth,
    requireRole(
        "Administrator",
        "Project Manager"
    ),
    userProfileController.showEditForm
);

router.post(
    "/user-profiles/edit/:id",
    requireAuth,
    requireRole(
        "Administrator",
        "Project Manager"
    ),
    userProfileController.update
);

router.get(
    "/user-profiles/delete/:id",
    requireAuth,
    requireRole(
        "Administrator"
    ),
    userProfileController.delete
);

module.exports = router;