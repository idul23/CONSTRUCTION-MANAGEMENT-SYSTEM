const { ProjectPhase, Project } = require("../models");


exports.index = async (req, res) => {
    try {
        const phases = await ProjectPhase.findAll({
            include: [
                {
                    model: Project
                }
            ],
            order: [["phase_id", "DESC"]]
        });

        res.render("phases/index", {
            user: req.user,
            phases
        });

    } catch (error) {
        console.error(error);
        res.status(500).send("Error loading project phases.");
    }
};


exports.showAddForm = async (req, res) => {
    try {
        const projects = await Project.findAll({
            order: [["project_name", "ASC"]]
        });

        res.render("phases/add", {
            user: req.user,
            projects,
            error: null
        });

    } catch (error) {
        console.error(error);
        res.status(500).send("Error loading add phase page.");
    }
};


exports.create = async (req, res) => {
    try {
        const {
            project_id,
            phase_name,
            start_date,
            end_date,
            planned_cost
        } = req.body;

        await ProjectPhase.create({
            project_id,
            phase_name,
            start_date,
            end_date: end_date || null,
            planned_cost: planned_cost || 0
        });

        res.redirect("/phases");

    } catch (error) {
        console.error(error);

        try {
            const projects = await Project.findAll({
                order: [["project_name", "ASC"]]
            });

            res.render("phases/add", {
                user: req.user,
                projects,
                error: "Unable to create project phase."
            });

        } catch (secondError) {
            console.error(secondError);
            res.status(500).send("Error creating project phase.");
        }
    }
};


exports.showEditForm = async (req, res) => {
    try {
        const phase = await ProjectPhase.findByPk(req.params.id);

        if (!phase) {
            return res.status(404).send("Project phase not found.");
        }

        const projects = await Project.findAll({
            order: [["project_name", "ASC"]]
        });

        res.render("phases/edit", {
            user: req.user,
            phase,
            projects,
            error: null
        });

    } catch (error) {
        console.error(error);
        res.status(500).send("Error loading edit phase page.");
    }
};


exports.update = async (req, res) => {
    try {
        const phase = await ProjectPhase.findByPk(req.params.id);

        if (!phase) {
            return res.status(404).send("Project phase not found.");
        }

        const {
            project_id,
            phase_name,
            start_date,
            end_date,
            planned_cost
        } = req.body;

        await phase.update({
            project_id,
            phase_name,
            start_date,
            end_date: end_date || null,
            planned_cost: planned_cost || 0
        });

        res.redirect("/phases");

    } catch (error) {
        console.error(error);
        res.status(500).send("Error updating project phase.");
    }
};


exports.delete = async (req, res) => {
    try {
        await ProjectPhase.destroy({
            where: {
                phase_id: req.params.id
            }
        });

        res.redirect("/phases");

    } catch (error) {
        console.error(error);
        res.status(500).send("Error deleting project phase.");
    }
};