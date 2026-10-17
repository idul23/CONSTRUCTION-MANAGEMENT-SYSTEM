const { AuditLog, User } = require("../models");

exports.index = async (req, res) => {
    try {
        const logs = await AuditLog.findAll({
            include: [
                {
                    model: User,
                    as: "changedByUser",
                    attributes: ["username"]
                }
            ],
            order: [["changed_at", "DESC"]]
        });

        res.render("audit-logs/index", {
            user: req.user,
            logs
        });

    } catch (error) {
        console.error("AUDIT LOG ERROR:");
        console.error(error);

        res.status(500).send("Error loading audit logs.");
    }
};