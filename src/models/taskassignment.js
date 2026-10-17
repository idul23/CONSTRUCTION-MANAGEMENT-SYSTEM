const { DataTypes } = require("sequelize");
const sequelize = require("../config/database");

const TaskAssignment = sequelize.define(
    "TaskAssignment",
    {
        assignment_id: {
            type: DataTypes.INTEGER,
            primaryKey: true,
            autoIncrement: true
        },

        task_id: {
            type: DataTypes.INTEGER,
            allowNull: false
        },

        worker_id: {
            type: DataTypes.INTEGER,
            allowNull: false
        },

        assigned_by: {
            type: DataTypes.INTEGER,
            allowNull: false
        },

        assigned_role: {
            type: DataTypes.STRING(100),
            allowNull: true
        },

        assigned_at: {
            type: DataTypes.DATE,
            allowNull: false,
            defaultValue: DataTypes.NOW
        },

        progress_pct: {
            type: DataTypes.DECIMAL(5, 2),
            allowNull: false,
            defaultValue: 0
        },

        status: {
            type: DataTypes.STRING(30),
            allowNull: false,
            defaultValue: "assigned"
        }
    },
    {
        tableName: "task_assignments",
        timestamps: false
    }
);

module.exports = TaskAssignment;