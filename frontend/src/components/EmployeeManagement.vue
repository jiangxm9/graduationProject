<template>
  <el-container>
    <el-header class="employee_mana_header">
      <el-input
        placeholder="请输入员工的编号(仅限店主使用)"
        v-model="employeeid"
        type="number"
        style="width: 200px;"
      ></el-input>
      <el-input
        placeholder="请输入员工的称呼"
        v-model="employeeName"
        style="margin-left: 10px;width: 200px;"
      ></el-input>
      <el-input
        placeholder="请输入员工的工作"
        v-model="employeeJob"
        style="margin-left: 10px;width: 200px;"
      ></el-input>
      <el-input
        placeholder="请输入员工的月薪"
        v-model="employeeWage"
        type="number"
        style="margin-left: 10px;width: 200px;"
      ></el-input>
      <el-button :disabled="identity" type="primary" round style="margin-left: 10px" @click="addEmployee">新增员工</el-button>
      <el-button :disabled="identity" type="primary" round style="margin-left: 10px" @click="modifyEmployee">更新员工信息</el-button>
    </el-header>
    <el-main class="employee_mana_main" v-loading="loading">
      <el-table
        :data="employeeList"
        ref="multipleTable"
        tooltip-effect="dark"
        border
        style="width: 100%"
      >
        <el-table-column prop="id" label="员工编号" width="180" align="center"></el-table-column>
        <el-table-column prop="name" label="员工名字" width="180" align="center"></el-table-column>
        <el-table-column prop="job" label="员工岗位" width="180" align="center"></el-table-column>
        <el-table-column prop="wage" label="员工月薪" width="180" align="center"></el-table-column>
        <el-table-column label="操作" align="center">
          <template slot-scope="scope">
            <el-button
              style="color: #ff0509"
              type="text"
              icon="el-icon-delete"
              :disabled="identity"
              @click="deleteEmployee(scope.row.id)"
            >开除</el-button>
            <el-input
              placeholder="请输入交给员工的任务"
              v-model="task[scope.row.id]"
              style="margin-left: 100px;width: 200px;"
            ></el-input>
            <el-button
              style="color: #67C23A"
              type="text"
              icon="el-icon-plus"
              :disabled="identity"
              @click="addTask(scope.row.id)"
            >任务</el-button>
          </template>
        </el-table-column>
      </el-table>
    </el-main>
  </el-container>
</template>
<script>
import { getRequest } from "../utils/api";
import { putRequest } from "../utils/api";
import { postRequest } from "../utils/api";
import { deleteRequest } from "../utils/api";
export default {
  mounted: function() {
    this.loading = true;
    this.loadEmployeeList();
    this.task = new Array(50);
    this.task.fill(null);
  },
  data() {
    return {
      employeeList: [],
      loading: false,
      employeeid: "",
      employeeName: "",
      employeeJob: "",
      employeeWage: "",
      task: [],
      identity: (this.$store.state.identity == 0)
    };
  },
  methods: {
    loadEmployeeList() {
      this.loading = true;
      getRequest(
        "http://localhost/backend/application/store/employee_list.php",
        { identity: this.$store.state.identity },
        this.$store.state.token
      ).then(resp => {
        var json = resp.data;
        if (resp.status == 200 && json.status == 200) {
          this.employeeList = json.data;
        } else {
          this.$alert("发生错误，获取订单列表失败！");
        }
      });
      this.loading = false;
    },
    addEmployee() {
      this.loading = true;
      putRequest(
        "http://localhost/backend/application/store/employee.php",
        {
          employeeid: this.employeeid,
          employeename: this.employeeName,
          employeejob: this.employeeJob,
          employeewage: this.employeeWage
        },
        this.$store.state.token
      ).then(resp => {
        var json = resp.data;
        if (resp.status == 200 && json.status == 200) {
          this.$message.success("添加员工成功");
        } else {
          this.$message.error("添加员工失败");
        }
      });
      this.loading = false;
      location.reload();
    },
    modifyEmployee() {
      this.loading = true;
      postRequest(
        "http://localhost/backend/application/store/employee.php",
        {
          employeeid: this.employeeid,
          employeename: this.employeeName,
          employeejob: this.employeeJob,
          employeewage: this.employeeWage
        },
        this.$store.state.token
      ).then(resp => {
        var json = resp.data;
        if (resp.status == 200 && json.status == 200) {
          this.$message.success("修改员工信息成功");
        } else {
          this.$message.error("修改员工信息失败");
        }
      });
      this.loading = false;
      location.reload();
    },
    deleteEmployee(employeeid) {
      this.loading = true;
      deleteRequest(
        "http://localhost/backend/application/store/employee.php",
        { employeeid: employeeid },
        this.$store.state.token
      ).then(resp => {
        var json = resp.data;
        if (resp.status == 200 && json.status == 200) {
          this.$message.success("开除员工成功");
        } else {
          this.$message.error("开除员工失败");
        }
      });
      this.loading = false;
      location.reload();
    },
    addTask(employeeid) {
      this.loading = true;
      putRequest(
        "http://localhost/backend/application/store/task.php",
        { employeeid: employeeid, task: this.task[employeeid] },
        this.$store.state.token
      ).then(resp => {
        var json = resp.data;
        if (resp.status == 200 && json.status == 200) {
          this.$notify({
            title: "添加任务成功",
            message:
              "添加任务id为：" +
              json.data.task_id +
              "  " +
              "任务开始时间：" +
              json.data.time,
            type: "success"
          });
        } else {
          this.$message.error("添加任务失败");
        }
      });

      clearTimeout(this.timer); //清除延迟执行

      this.timer = setTimeout(() => {
        //设置延迟执行
        this.task.fill(null);
        location.reload();
      }, 500);
      this.loading = false;
    }
  }
};
</script>
<style>
.employee_mana_header {
  background-color: #ececec;
  padding-left: 5px;
  display: flex;
  justify-content: flex-start;
}

.employee_mana_main {
  display: flex;
  flex-direction: column;
  padding-left: 5px;
  background-color: #ececec;
}
</style>