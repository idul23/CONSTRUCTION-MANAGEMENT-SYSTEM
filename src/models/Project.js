const { DataTypes } = require("sequelize");
const sequelize = require("../config/database");

const Project = sequelize.define(
    "Project",
    {
        project_id: {
            type: DataTypes.INTEGER,
            primaryKey: true,
            autoIncrement: true
        },

        client_id: {
            type: DataTypes.INTEGER,
            allowNull: false
        },

        manager_id: {
            type: DataTypes.INTEGER,
            allowNull: false
        },

        project_name: {
            type: DataTypes.STRING(150),
            allowNull: false
        },

        location: {
            type: DataTypes.STRING(150),
            allowNull: false
        },

        start_date: {
            type: DataTypes.DATEONLY,
            allowNull: false
        },

        end_date: {
            type: DataTypes.DATEONLY,
            allowNull: true
        },

        total_budget: {
            type: DataTypes.DECIMAL(14, 2),
            allowNull: false
        },

        remaining_budget: {
            type: DataTypes.DECIMAL(14, 2),
            allowNull: false
        },

        status: {
            type: DataTypes.STRING(30),
            allowNull: false
        }
    },
    {
        tableName: "projects",
        timestamps: false
    }
);

module.exports = Project;