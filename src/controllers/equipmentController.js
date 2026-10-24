
const { Equipment } = require("../models");

exports.index = async (req, res) => {
    try {
        if (!Equipment) {
            throw new Error("Equipment model is missing from src/models/index.js");
        }

        const equipment = await Equipment.findAll({
            order: [["equipment_id", "DESC"]]
        });

        return res.render("equipment/index", {
            user: req.user,
            equipment: equipment || []
        });
    } catch (error) {
        console.error("EQUIPMENT LIST ERROR:", error);

        return res.status(500).send(`
            <html>
            <head>
                <title>Equipment Error</title>
                <style>
                    body {
                        font-family: Arial, sans-serif;
                        background: #f3f5f9;
                        padding: 40px;
                        color: #263445;
                    }
                    .box {
                        max-width: 700px;
                        margin: 30px auto;
                        background: white;
                        padding: 30px;
                        border-radius: 10px;
                        border-top: 5px solid #f28c28;
                    }
                    h2 { color: #142b4a; }
                    a {
                        display: inline-block;
                        background: #f28c28;
                        color: white;
                        text-decoration: none;
                        padding: 11px 16px;
                        border-radius: 6px;
                    }
                </style>
            </head>
            <body>
                <div class="box">
                    <h2>Unable to Load Equipment</h2>
                    <p>The equipment query failed. Check your VS Code terminal for the exact error.</p>
                    <a href="/dashboard">Back to Dashboard</a>
                </div>
            </body>
            </html>
        `);
    }
};

exports.showAddForm = (req, res) => {
    return res.render("equipment/add", {
        user: req.user,
        error: null
    });
};

exports.create = async (req, res) => {
    try {
        const {
            name,
            equip_type,
            serial_no,
            daily_rate,
            condition
        } = req.body;

        if (!name || !name.trim()) {
            return res.render("equipment/add", {
                user: req.user,
                error: "Equipment name is required."
            });
        }

        const rate = Number(daily_rate || 0);

        if (!Number.isFinite(rate) || rate < 0) {
            return res.render("equipment/add", {
                user: req.user,
                error: "Daily rate must be a valid non-negative number."
            });
        }

        await Equipment.create({
            name: name.trim(),
            equip_type: equip_type || null,
            serial_no: serial_no || null,
            daily_rate: rate,
            condition: condition || null
        });

        return res.redirect("/equipment");
    } catch (error) {
        console.error("EQUIPMENT CREATE ERROR:", error);

        return res.status(500).send(
            "Unable to save equipment. Check the VS Code terminal for the actual error."
        );
    }
};

exports.showEditForm = async (req, res) => {
    try {
        const equipment = await Equipment.findByPk(req.params.id);

        if (!equipment) {
            return res.status(404).send("Equipment not found.");
        }

        return res.render("equipment/edit", {
            user: req.user,
            equipment,
            error: null
        });
    } catch (error) {
        console.error("EQUIPMENT EDIT FORM ERROR:", error);

        return res.status(500).send(
            "Unable to load equipment edit form. Check the VS Code terminal."
        );
    }
};

exports.update = async (req, res) => {
    try {
        const equipment = await Equipment.findByPk(req.params.id);

        if (!equipment) {
            return res.status(404).send("Equipment not found.");
        }

        const {
            name,
            equip_type,
            serial_no,
            daily_rate,
            condition
        } = req.body;

        if (!name || !name.trim()) {
            return res.render("equipment/edit", {
                user: req.user,
                equipment,
                error: "Equipment name is required."
            });
        }

        const rate = Number(daily_rate || 0);

        if (!Number.isFinite(rate) || rate < 0) {
            return res.render("equipment/edit", {
                user: req.user,
                equipment,
                error: "Daily rate must be a valid non-negative number."
            });
        }

        await equipment.update({
            name: name.trim(),
            equip_type: equip_type || null,
            serial_no: serial_no || null,
            daily_rate: rate,
            condition: condition || null
        });

        return res.redirect("/equipment");
    } catch (error) {
        console.error("EQUIPMENT UPDATE ERROR:", error);

        return res.status(500).send(
            "Unable to update equipment. Check the VS Code terminal."
        );
    }
};

exports.delete = async (req, res) => {
    try {
        const equipment = await Equipment.findByPk(req.params.id);

        if (!equipment) {
            return res.status(404).send("Equipment not found.");
        }

        await equipment.destroy();

        return res.redirect("/equipment");
    } catch (error) {
        console.error("EQUIPMENT DELETE ERROR:", error);

        return res.status(500).send(
            "Unable to delete equipment. Check the VS Code terminal."
        );
    }
};
