const { DataTypes } = require("sequelize");
const sequelize = require("../config/database");

const Task = sequelize.define(
    "Task",
    {
        task_id: {
            type: DataTypes.INTEGER,
            primaryKey: true,
            autoIncrement: true
        },

        phase_id: {
            type: DataTypes.INTEGER,
            allowNull: false
        },

        task_name: {
            type: DataTypes.STRING(150),
            allowNull: false
        },

        start_date: {
            type: DataTypes.DATEONLY,
            allowNull: true
        },

        due_date: {
            type: DataTypes.DATEONLY,
            allowNull: true
        },

        progress_pct: {
            type: DataTypes.DECIMAL(5, 2),
            allowNull: false,
            defaultValue: 0
        },

        status: {
            type: DataTypes.STRING(30),
            allowNull: false,
            defaultValue: "pending"
        }
    },
    {
        tableName: "tasks",
        timestamps: false
    }
);

module.exports = Task;