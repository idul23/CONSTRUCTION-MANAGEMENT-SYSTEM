const express = require("express");

const router = express.Router();

const projectMemberController = require("../controllers/projectMemberController");

const {
    requireAuth,
    requireRole
} = require("../middleware/authMiddleware");

router.get(
    "/project-members",
    requireAuth,
    requireRole(
        "Administrator",
        "Project Manager",
        "Site Staff"
    ),
    projectMemberController.index
);

router.get(
    "/project-members/add",
    requireAuth,
    requireRole(
        "Administrator",
        "Project Manager"
    ),
    projectMemberController.showAddForm
);

router.post(
    "/project-members/add",
    requireAuth,
    requireRole(
        "Administrator",
        "Project Manager"
    ),
    projectMemberController.create
);

router.get(
    "/project-members/edit/:project_id/:user_id",
    requireAuth,
    requireRole(
        "Administrator",
        "Project Manager"
    ),
    projectMemberController.showEditForm
);

router.post(
    "/project-members/edit/:project_id/:user_id",
    requireAuth,
    requireRole(
        "Administrator",
        "Project Manager"
    ),
    projectMemberController.update
);

router.get(
    "/project-members/delete/:project_id/:user_id",
    requireAuth,
    requireRole(
        "Administrator",
        "Project Manager"
    ),
    projectMemberController.delete
);

module.exports = router;