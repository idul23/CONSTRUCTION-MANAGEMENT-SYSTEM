const express = require("express");

const router = express.Router();

const workerSkillController = require("../controllers/workerSkillController");

const {
    requireAuth,
    requireRole
} = require("../middleware/authMiddleware");

router.get(
    "/worker-skills",
    requireAuth,
    requireRole(
        "Administrator",
        "Project Manager",
        "Site Staff"
    ),
    workerSkillController.index
);

router.get(
    "/worker-skills/add",
    requireAuth,
    requireRole(
        "Administrator",
        "Project Manager"
    ),
    workerSkillController.showAddForm
);

router.post(
    "/worker-skills/add",
    requireAuth,
    requireRole(
        "Administrator",
        "Project Manager"
    ),
    workerSkillController.create
);

router.get(
    "/worker-skills/edit/:worker_id/:skill_id",
    requireAuth,
    requireRole(
        "Administrator",
        "Project Manager"
    ),
    workerSkillController.showEditForm
);

router.post(
    "/worker-skills/edit/:worker_id/:skill_id",
    requireAuth,
    requireRole(
        "Administrator",
        "Project Manager"
    ),
    workerSkillController.update
);

router.get(
    "/worker-skills/delete/:worker_id/:skill_id",
    requireAuth,
    requireRole(
        "Administrator",
        "Project Manager"
    ),
    workerSkillController.delete
);

module.exports = router;