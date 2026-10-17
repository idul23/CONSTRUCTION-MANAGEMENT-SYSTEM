const { DataTypes } = require("sequelize");
const sequelize = require("../config/database");

const AuditLog = sequelize.define(
    "AuditLog",
    {
        log_id: {
            type: DataTypes.BIGINT,
            primaryKey: true,
            autoIncrement: true
        },

        changed_by: {
            type: DataTypes.INTEGER,
            allowNull: true
        },

        table_name: {
            type: DataTypes.STRING(100),
            allowNull: false
        },

        record_id: {
            type: DataTypes.INTEGER,
            allowNull: true
        },

        action_type: {
            type: DataTypes.STRING(10),
            allowNull: false
        },

        old_values: {
            type: DataTypes.JSONB,
            allowNull: true
        },

        new_values: {
            type: DataTypes.JSONB,
            allowNull: true
        },

        changed_at: {
            type: DataTypes.DATE,
            allowNull: false,
            defaultValue: DataTypes.NOW
        }
    },
    {
        tableName: "audit_logs",
        timestamps: false
    }
);

module.exports = AuditLog;