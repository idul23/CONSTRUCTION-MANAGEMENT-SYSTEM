const {
    WorkerSkill,
    Worker,
    Skill
} = require("../models");

exports.index = async (req, res) => {
    try {
        const workerSkills = await WorkerSkill.findAll({
            include: [
                {
                    model: Worker
                },
                {
                    model: Skill
                }
            ],
            order: [
                ["worker_id", "DESC"],
                ["skill_id", "DESC"]
            ]
        });

        res.render("worker-skills/index", {
            user: req.user,
            workerSkills
        });

    } catch (error) {
        console.error("WORKER SKILLS LIST ERROR:");
        console.error(error);

        res.status(500).send("Error loading worker skills.");
    }
};

exports.showAddForm = async (req, res) => {
    try {
        const workers = await Worker.findAll({
            where: {
                is_active: true
            },
            order: [
                ["first_name", "ASC"],
                ["last_name", "ASC"]
            ]
        });

        const skills = await Skill.findAll({
            order: [
                ["skill_name", "ASC"]
            ]
        });

        res.render("worker-skills/add", {
            user: req.user,
            workers,
            skills,
            error: null
        });

    } catch (error) {
        console.error("WORKER SKILLS ADD FORM ERROR:");
        console.error(error);

        res.status(500).send("Error loading worker skill form.");
    }
};

exports.create = async (req, res) => {
    try {
        const {
            worker_id,
            skill_id,
            proficiency,
            years_exp,
            certified_date
        } = req.body;

        await WorkerSkill.create({
            worker_id: Number(worker_id),
            skill_id: Number(skill_id),
            proficiency: proficiency || null,
            years_exp: Number(years_exp || 0),
            certified_date: certified_date || null
        });

        res.redirect("/worker-skills");

    } catch (error) {
        console.error("WORKER SKILLS CREATE ERROR:");
        console.error(error);

        const workers = await Worker.findAll({
            where: {
                is_active: true
            },
            order: [
                ["first_name", "ASC"],
                ["last_name", "ASC"]
            ]
        });

        const skills = await Skill.findAll({
            order: [
                ["skill_name", "ASC"]
            ]
        });

        res.render("worker-skills/add", {
            user: req.user,
            workers,
            skills,
            error: error.message
        });
    }
};

exports.showEditForm = async (req, res) => {
    try {
        const worker_id = Number(req.params.worker_id);
        const skill_id = Number(req.params.skill_id);

        const workerSkill = await WorkerSkill.findOne({
            where: {
                worker_id,
                skill_id
            }
        });

        if (!workerSkill) {
            return res.status(404).send("Worker skill not found.");
        }

        const workers = await Worker.findAll({
            order: [
                ["first_name", "ASC"],
                ["last_name", "ASC"]
            ]
        });

        const skills = await Skill.findAll({
            order: [
                ["skill_name", "ASC"]
            ]
        });

        res.render("worker-skills/edit", {
            user: req.user,
            workerSkill,
            workers,
            skills,
            error: null
        });

    } catch (error) {
        console.error("WORKER SKILLS EDIT FORM ERROR:");
        console.error(error);

        res.status(500).send("Error loading worker skill.");
    }
};

exports.update = async (req, res) => {
    try {
        const oldWorkerId = Number(req.params.worker_id);
        const oldSkillId = Number(req.params.skill_id);

        const workerSkill = await WorkerSkill.findOne({
            where: {
                worker_id: oldWorkerId,
                skill_id: oldSkillId
            }
        });

        if (!workerSkill) {
            return res.status(404).send("Worker skill not found.");
        }

        const {
            worker_id,
            skill_id,
            proficiency,
            years_exp,
            certified_date
        } = req.body;

        const newWorkerId = Number(worker_id);
        const newSkillId = Number(skill_id);

        if (
            newWorkerId !== oldWorkerId ||
            newSkillId !== oldSkillId
        ) {
            const existing = await WorkerSkill.findOne({
                where: {
                    worker_id: newWorkerId,
                    skill_id: newSkillId
                }
            });

            if (existing) {
                return res.status(400).send(
                    "This worker already has this skill."
                );
            }
        }

        await workerSkill.destroy();

        await WorkerSkill.create({
            worker_id: newWorkerId,
            skill_id: newSkillId,
            proficiency: proficiency || null,
            years_exp: Number(years_exp || 0),
            certified_date: certified_date || null
        });

        res.redirect("/worker-skills");

    } catch (error) {
        console.error("WORKER SKILLS UPDATE ERROR:");
        console.error(error);

        res.status(500).send(
            "Error updating worker skill: " + error.message
        );
    }
};

exports.delete = async (req, res) => {
    try {
        await WorkerSkill.destroy({
            where: {
                worker_id: Number(req.params.worker_id),
                skill_id: Number(req.params.skill_id)
            }
        });

        res.redirect("/worker-skills");

    } catch (error) {
        console.error("WORKER SKILLS DELETE ERROR:");
        console.error(error);

        res.status(500).send("Error deleting worker skill.");
    }
};