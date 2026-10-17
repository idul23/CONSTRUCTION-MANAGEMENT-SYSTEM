const { DataTypes } = require("sequelize");
const sequelize = require("../config/database");

const ProjectPhase = sequelize.define(
    "ProjectPhase",
    {
        phase_id: {
            type: DataTypes.INTEGER,
            primaryKey: true,
            autoIncrement: true
        },

        project_id: {
            type: DataTypes.INTEGER,
            allowNull: false
        },

        phase_name: {
            type: DataTypes.STRING(120),
            allowNull: false
        },

        start_date: {
            type: DataTypes.DATEONLY,
            allowNull: true
        },

        end_date: {
            type: DataTypes.DATEONLY,
            allowNull: true
        },

        planned_cost: {
            type: DataTypes.DECIMAL(14, 2),
            allowNull: false,
            defaultValue: 0
        }
    },
    {
        tableName: "project_phases",
        timestamps: false
    }
);

module.exports = ProjectPhase;