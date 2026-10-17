const express = require("express");
const path = require("path");
const cookieParser = require("cookie-parser");

require("dotenv").config();

const sequelize = require("./config/database");

require("./models");

const authRoutes = require("./routes/authRoutes");
const dashboardRoutes = require("./routes/dashboardRoutes");
const projectRoutes = require("./routes/projectRoutes");
const workerRoutes = require("./routes/workerRoutes");
const taskRoutes = require("./routes/taskRoutes");
const phaseRoutes = require("./routes/phaseRoutes");
const attendanceRoutes = require("./routes/attendanceRoutes");
const assignmentRoutes = require("./routes/assignmentRoutes");
const materialRoutes = require("./routes/materialRoutes");
const supplierRoutes = require("./routes/supplierRoutes");
const materialUsageRoutes = require("./routes/materialUsageRoutes");
const equipmentRoutes = require("./routes/equipmentRoutes");
const equipmentUsageRoutes = require("./routes/equipmentUsageRoutes");
const expenseRoutes = require("./routes/expenseRoutes");
const reportRoutes = require("./routes/reportRoutes");
const auditRoutes = require("./routes/auditRoutes");
const userRoutes = require("./routes/userRoutes");
const workerSkillRoutes = require("./routes/workerSkillRoutes");
const projectMemberRoutes = require("./routes/projectMemberRoutes");
const userProfileRoutes = require("./routes/userProfileRoutes");

const app = express();

app.set("view engine", "ejs");
app.set("views", path.join(__dirname, "views"));

app.use(express.urlencoded({ extended: true }));
app.use(express.json());
app.use(cookieParser());

app.use(express.static(path.join(__dirname, "public")));

app.use("/", authRoutes);
app.use("/", dashboardRoutes);
app.use("/", projectRoutes);
app.use("/", workerRoutes);
app.use("/", taskRoutes);
app.use("/", phaseRoutes);
app.use("/", attendanceRoutes);
app.use("/", assignmentRoutes);
app.use("/", materialRoutes);
app.use("/", supplierRoutes);
app.use("/", materialUsageRoutes);
app.use("/", equipmentRoutes);
app.use("/", equipmentUsageRoutes);
app.use("/", expenseRoutes);
app.use("/", reportRoutes);
app.use("/", auditRoutes);
app.use("/", userRoutes);
app.use("/", workerSkillRoutes);
app.use("/", projectMemberRoutes);
app.use("/", userProfileRoutes);

app.get("/", (req, res) => {
    res.redirect("/login");
});

const PORT = process.env.PORT || 3000;

async function startServer() {
    try {
        await sequelize.authenticate();

        console.log("PostgreSQL connected successfully.");
        console.log("All models loaded successfully.");

        app.listen(PORT, () => {
            console.log(`Server running at http://localhost:${PORT}`);
        });
    } catch (error) {
        console.error("Database connection failed:");
        console.error(error.message);
    }
}

startServer();