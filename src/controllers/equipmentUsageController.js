const {
    EquipmentUsage,
    Task,
    Equipment
} = require("../models");

exports.index = async (req, res) => {
    try {
        const usageRecords = await EquipmentUsage.findAll({
            order: [["usage_id", "DESC"]]
        });

        const usage = [];

        for (const record of usageRecords) {
            const task = await Task.findByPk(record.task_id);
            const equipment = await Equipment.findByPk(record.equipment_id);

            usage.push({
                usage_id: record.usage_id,
                task: task,
                equipment: equipment,
                date_used: record.date_used,
                hours_used: record.hours_used,
                usage_cost: record.usage_cost
            });
        }

        res.render("equipment-usage/index", {
            user: req.user,
            usage
        });
    } catch (error) {
        console.error("EQUIPMENT USAGE LIST ERROR:");
        console.error(error);
        res.status(500).send("Error loading equipment usage.");
    }
};

exports.showAddForm = async (req, res) => {
    try {
        const tasks = await Task.findAll({
            order: [["task_id", "DESC"]]
        });

        const equipment = await Equipment.findAll({
            order: [["name", "ASC"]]
        });

        res.render("equipment-usage/add", {
            user: req.user,
            tasks,
            equipment,
            error: null
        });
    } catch (error) {
        console.error("EQUIPMENT USAGE ADD FORM ERROR:");
        console.error(error);
        res.status(500).send("Error loading equipment usage form.");
    }
};

exports.create = async (req, res) => {
    try {
        const {
            task_id,
            equipment_id,
            date_used,
            hours_used
        } = req.body;

        const equipment = await Equipment.findByPk(equipment_id);

        if (!equipment) {
            return res.status(404).send("Equipment not found.");
        }

        const hours = Number(hours_used || 0);
        const dailyRate = Number(equipment.daily_rate || 0);

        const usageCost = dailyRate * hours;

        await EquipmentUsage.create({
            task_id: Number(task_id),
            equipment_id: Number(equipment_id),
            recorded_by: req.user.user_id,
            date_used: date_used,
            hours_used: hours,
            usage_cost: usageCost
        });

        res.redirect("/equipment-usage");
    } catch (error) {
        console.error("EQUIPMENT USAGE CREATE ERROR:");
        console.error(error);
        res.status(500).send(
            "Error creating equipment usage: " + error.message
        );
    }
};

exports.showEditForm = async (req, res) => {
    try {
        const usageRecord = await EquipmentUsage.findByPk(req.params.id);

        if (!usageRecord) {
            return res.status(404).send(
                "Equipment usage record not found."
            );
        }

        const tasks = await Task.findAll({
            order: [["task_id", "DESC"]]
        });

        const equipment = await Equipment.findAll({
            order: [["name", "ASC"]]
        });

        res.render("equipment-usage/edit", {
            user: req.user,
            usageRecord,
            tasks,
            equipment,
            error: null
        });
    } catch (error) {
        console.error("EQUIPMENT USAGE EDIT FORM ERROR:");
        console.error(error);
        res.status(500).send(
            "Error loading equipment usage."
        );
    }
};

exports.update = async (req, res) => {
    try {
        const usageRecord = await EquipmentUsage.findByPk(req.params.id);

        if (!usageRecord) {
            return res.status(404).send(
                "Equipment usage record not found."
            );
        }

        const {
            task_id,
            equipment_id,
            date_used,
            hours_used
        } = req.body;

        const equipment = await Equipment.findByPk(equipment_id);

        if (!equipment) {
            return res.status(404).send("Equipment not found.");
        }

        const hours = Number(hours_used || 0);
        const dailyRate = Number(equipment.daily_rate || 0);

        const usageCost = dailyRate * hours;

        await usageRecord.update({
            task_id: Number(task_id),
            equipment_id: Number(equipment_id),
            date_used: date_used,
            hours_used: hours,
            usage_cost: usageCost
        });

        res.redirect("/equipment-usage");
    } catch (error) {
        console.error("EQUIPMENT USAGE UPDATE ERROR:");
        console.error(error);
        res.status(500).send(
            "Error updating equipment usage: " + error.message
        );
    }
};

exports.delete = async (req, res) => {
    try {
        await EquipmentUsage.destroy({
            where: {
                usage_id: req.params.id
            }
        });

        res.redirect("/equipment-usage");
    } catch (error) {
        console.error("EQUIPMENT USAGE DELETE ERROR:");
        console.error(error);
        res.status(500).send(
            "Error deleting equipment usage."
        );
    }
};