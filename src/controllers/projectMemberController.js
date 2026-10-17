const {
    ProjectMember,
    Project,
    User
} = require("../models");

exports.index = async (req, res) => {
    try {
        const projectMembers = await ProjectMember.findAll({
            include: [
                {
                    model: Project
                },
                {
                    model: User
                }
            ],
            order: [
                ["project_id", "DESC"],
                ["user_id", "DESC"]
            ]
        });

        res.render("project-members/index", {
            user: req.user,
            projectMembers
        });

    } catch (error) {
        console.error("PROJECT MEMBERS LIST ERROR:");
        console.error(error);

        res.status(500).send("Error loading project members.");
    }
};

exports.showAddForm = async (req, res) => {
    try {
        const projects = await Project.findAll({
            order: [
                ["project_id", "DESC"]
            ]
        });

        const users = await User.findAll({
            where: {
                is_active: true
            },
            order: [
                ["username", "ASC"]
            ]
        });

        res.render("project-members/add", {
            user: req.user,
            projects,
            users,
            error: null
        });

    } catch (error) {
        console.error("PROJECT MEMBERS ADD FORM ERROR:");
        console.error(error);

        res.status(500).send("Error loading project member form.");
    }
};

exports.create = async (req, res) => {
    try {
        const {
            project_id,
            user_id,
            project_role,
            assigned_date,
            is_active
        } = req.body;

        await ProjectMember.create({
            project_id: Number(project_id),
            user_id: Number(user_id),
            project_role,
            assigned_date: assigned_date || null,
            is_active: is_active === "on"
        });

        res.redirect("/project-members");

    } catch (error) {
        console.error("PROJECT MEMBERS CREATE ERROR:");
        console.error(error);

        const projects = await Project.findAll({
            order: [
                ["project_id", "DESC"]
            ]
        });

        const users = await User.findAll({
            where: {
                is_active: true
            },
            order: [
                ["username", "ASC"]
            ]
        });

        res.render("project-members/add", {
            user: req.user,
            projects,
            users,
            error: error.message
        });
    }
};

exports.showEditForm = async (req, res) => {
    try {
        const project_id = Number(req.params.project_id);
        const user_id = Number(req.params.user_id);

        const projectMember = await ProjectMember.findOne({
            where: {
                project_id,
                user_id
            }
        });

        if (!projectMember) {
            return res.status(404).send("Project member not found.");
        }

        const projects = await Project.findAll({
            order: [
                ["project_id", "DESC"]
            ]
        });

        const users = await User.findAll({
            order: [
                ["username", "ASC"]
            ]
        });

        res.render("project-members/edit", {
            user: req.user,
            projectMember,
            projects,
            users,
            error: null
        });

    } catch (error) {
        console.error("PROJECT MEMBERS EDIT FORM ERROR:");
        console.error(error);

        res.status(500).send("Error loading project member.");
    }
};

exports.update = async (req, res) => {
    try {
        const oldProjectId = Number(req.params.project_id);
        const oldUserId = Number(req.params.user_id);

        const projectMember = await ProjectMember.findOne({
            where: {
                project_id: oldProjectId,
                user_id: oldUserId
            }
        });

        if (!projectMember) {
            return res.status(404).send("Project member not found.");
        }

        const {
            project_id,
            user_id,
            project_role,
            assigned_date,
            is_active
        } = req.body;

        const newProjectId = Number(project_id);
        const newUserId = Number(user_id);

        if (
            newProjectId !== oldProjectId ||
            newUserId !== oldUserId
        ) {
            const existing = await ProjectMember.findOne({
                where: {
                    project_id: newProjectId,
                    user_id: newUserId
                }
            });

            if (existing) {
                return res.status(400).send(
                    "This user is already assigned to this project."
                );
            }
        }

        await projectMember.destroy();

        await ProjectMember.create({
            project_id: newProjectId,
            user_id: newUserId,
            project_role,
            assigned_date: assigned_date || null,
            is_active: is_active === "on"
        });

        res.redirect("/project-members");

    } catch (error) {
        console.error("PROJECT MEMBERS UPDATE ERROR:");
        console.error(error);

        res.status(500).send(
            "Error updating project member: " + error.message
        );
    }
};

exports.delete = async (req, res) => {
    try {
        await ProjectMember.destroy({
            where: {
                project_id: Number(req.params.project_id),
                user_id: Number(req.params.user_id)
            }
        });

        res.redirect("/project-members");

    } catch (error) {
        console.error("PROJECT MEMBERS DELETE ERROR:");
        console.error(error);

        res.status(500).send("Error deleting project member.");
    }
};