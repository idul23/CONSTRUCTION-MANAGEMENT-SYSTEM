const { DataTypes } = require("sequelize");
const sequelize = require("../config/database");

const Attendance = sequelize.define(
    "Attendance",
    {
        attendance_id: {
            type: DataTypes.INTEGER,
            primaryKey: true,
            autoIncrement: true
        },

        assignment_id: {
            type: DataTypes.INTEGER,
            allowNull: false
        },

        recorded_by: {
            type: DataTypes.INTEGER,
            allowNull: false
        },

        work_date: {
            type: DataTypes.DATEONLY,
            allowNull: false
        },

        time_in: {
            type: DataTypes.TIME,
            allowNull: false
        },

        time_out: {
            type: DataTypes.TIME,
            allowNull: false
        },

        hours_worked: {
            type: DataTypes.DECIMAL(6, 2),
            allowNull: false
        },

        labor_cost: {
            type: DataTypes.DECIMAL(12, 2),
            allowNull: false
        }
    },
    {
        tableName: "attendance",
        timestamps: false
    }
);

module.exports = Attendance;