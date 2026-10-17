const {
    Attendance,
    TaskAssignment,
    Task,
    Worker
} = require("../models");


exports.index = async (req, res) => {
    try {

        const attendanceRecords =
            await Attendance.findAll({
                order: [
                    ["attendance_id", "DESC"]
                ]
            });


        const attendance = [];


        for (const record of attendanceRecords) {

            const assignment =
                await TaskAssignment.findByPk(
                    record.assignment_id
                );


            let task = null;
            let worker = null;


            if (assignment) {

                task =
                    await Task.findByPk(
                        assignment.task_id
                    );


                worker =
                    await Worker.findByPk(
                        assignment.worker_id
                    );

            }


            attendance.push({
                attendance_id:
                    record.attendance_id,

                assignment_id:
                    record.assignment_id,

                work_date:
                    record.work_date,

                time_in:
                    record.time_in,

                time_out:
                    record.time_out,

                hours_worked:
                    record.hours_worked,

                labor_cost:
                    record.labor_cost,

                TaskAssignment: {
                    Task: task,
                    Worker: worker
                }
            });

        }


        res.render("attendance/index", {
            user: req.user,
            attendance
        });


    } catch (error) {

        console.error(
            "ATTENDANCE LIST ERROR:"
        );

        console.error(error);


        res.status(500).send(
            "Error loading attendance: " +
            error.message
        );

    }
};


exports.showAddForm = async (req, res) => {
    try {

        const assignments =
            await TaskAssignment.findAll({
                order: [
                    ["assignment_id", "DESC"]
                ]
            });


        const assignmentList = [];


        for (const assignment of assignments) {

            const task =
                await Task.findByPk(
                    assignment.task_id
                );


            const worker =
                await Worker.findByPk(
                    assignment.worker_id
                );


            assignmentList.push({
                assignment_id:
                    assignment.assignment_id,

                Task: task,

                Worker: worker
            });

        }


        res.render("attendance/add", {
            user: req.user,
            assignments:
                assignmentList,
            error: null
        });


    } catch (error) {

        console.error(
            "ATTENDANCE ADD FORM ERROR:"
        );

        console.error(error);


        res.status(500).send(
            "Error loading attendance form: " +
            error.message
        );

    }
};


exports.create = async (req, res) => {
    try {

        const {
            assignment_id,
            work_date,
            time_in,
            time_out,
            hours_worked
        } = req.body;


        const assignment =
            await TaskAssignment.findByPk(
                assignment_id
            );


        if (!assignment) {

            return res.status(404).send(
                "Task assignment not found."
            );

        }


        const worker =
            await Worker.findByPk(
                assignment.worker_id
            );


        const hours =
            Number(hours_worked || 0);


        const hourlyRate =
            Number(
                worker?.hourly_rate || 0
            );


        const laborCost =
            hours * hourlyRate;


        await Attendance.create({

            assignment_id:
                Number(assignment_id),

            recorded_by:
                req.user.user_id,

            work_date:
                work_date,

            time_in:
                time_in || null,

            time_out:
                time_out || null,

            hours_worked:
                hours,

            labor_cost:
                laborCost

        });


        res.redirect(
            "/attendance"
        );


    } catch (error) {

        console.error(
            "ATTENDANCE CREATE ERROR:"
        );

        console.error(error);


        res.status(500).send(
            "Error creating attendance: " +
            error.message
        );

    }
};


exports.showEditForm = async (req, res) => {
    try {

        const attendance =
            await Attendance.findByPk(
                req.params.id
            );


        if (!attendance) {

            return res.status(404).send(
                "Attendance record not found."
            );

        }


        const assignments =
            await TaskAssignment.findAll({
                order: [
                    ["assignment_id", "DESC"]
                ]
            });


        const assignmentList = [];


        for (const assignment of assignments) {

            const task =
                await Task.findByPk(
                    assignment.task_id
                );


            const worker =
                await Worker.findByPk(
                    assignment.worker_id
                );


            assignmentList.push({
                assignment_id:
                    assignment.assignment_id,

                Task: task,

                Worker: worker
            });

        }


        res.render(
            "attendance/edit",
            {
                user: req.user,
                attendance,
                assignments:
                    assignmentList,
                error: null
            }
        );


    } catch (error) {

        console.error(
            "ATTENDANCE EDIT FORM ERROR:"
        );

        console.error(error);


        res.status(500).send(
            "Error loading attendance: " +
            error.message
        );

    }
};


exports.update = async (req, res) => {
    try {

        const attendance =
            await Attendance.findByPk(
                req.params.id
            );


        if (!attendance) {

            return res.status(404).send(
                "Attendance record not found."
            );

        }


        const {
            assignment_id,
            work_date,
            time_in,
            time_out,
            hours_worked
        } = req.body;


        const assignment =
            await TaskAssignment.findByPk(
                assignment_id
            );


        if (!assignment) {

            return res.status(404).send(
                "Task assignment not found."
            );

        }


        const worker =
            await Worker.findByPk(
                assignment.worker_id
            );


        const hours =
            Number(hours_worked || 0);


        const hourlyRate =
            Number(
                worker?.hourly_rate || 0
            );


        const laborCost =
            hours * hourlyRate;


        await attendance.update({

            assignment_id:
                Number(assignment_id),

            work_date:
                work_date,

            time_in:
                time_in || null,

            time_out:
                time_out || null,

            hours_worked:
                hours,

            labor_cost:
                laborCost

        });


        res.redirect(
            "/attendance"
        );


    } catch (error) {

        console.error(
            "ATTENDANCE UPDATE ERROR:"
        );

        console.error(error);


        res.status(500).send(
            "Error updating attendance: " +
            error.message
        );

    }
};


exports.delete = async (req, res) => {
    try {

        await Attendance.destroy({

            where: {
                attendance_id:
                    req.params.id
            }

        });


        res.redirect(
            "/attendance"
        );


    } catch (error) {

        console.error(
            "ATTENDANCE DELETE ERROR:"
        );

        console.error(error);


        res.status(500).send(
            "Error deleting attendance: " +
            error.message
        );

    }
};