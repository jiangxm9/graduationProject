<template>
  <el-container>
    <el-header class="task_mana_header">
      <el-input
        placeholder="请输入员工的编号(仅限店主使用)"
        v-model="employeeid"
        type="number"
        style="width: 400px;"
      ></el-input>
      <el-button
        type="primary"
        round
        style="margin-left: 10px"
        icon="el-icon-search"
        :disabled="identity"
        @click="getTaskListByEmpid"
      >获取员工任务</el-button>
      <el-button type="primary" round style="margin-left: 10px" @click="loadTaskList">获取所有任务</el-button>
    </el-header>
    <el-main class="task_mana_main" v-loading="loading">
      <el-table
        :data="taskList"
        ref="multipleTable"
        tooltip-effect="dark"
        border
        style="width: 100%"
      >
        <el-table-column prop="id" label="任务编号" width="100" align="center"></el-table-column>
        <el-table-column prop="employeename" label="员工名字" width="100" align="center"></el-table-column>
        <el-table-column prop="employeejob" label="员工岗位" width="100" align="center"></el-table-column>
        <el-table-column prop="task" label="员工任务" width="180" align="center"></el-table-column>
        <el-table-column prop="evaluation" label="评价" width="180" align="center">
          <template slot-scope="scope">
            <div class="block">
              <el-rate disabled v-model="scope.row.evaluation" :colors="colors"></el-rate>
            </div>
          </template>
        </el-table-column>
        <el-table-column prop="remark" label="备注" width="180" align="center"></el-table-column>
        <el-table-column prop="tasktime" label="下达时间" width="100" align="center"></el-table-column>
        <el-table-column label="操作" align="center">
          <template slot-scope="scope">
            <el-button
              style="color: #ff0509"
              type="text"
              icon="el-icon-delete"
              :disabled="identity"
              @click="deleteTask(scope.row.id)"
            >删除</el-button>
            <el-dialog title="修改任务信息" :visible.sync="taskDialogVisible" width="30%" center>
              <el-input v-model="newTask" placeholder="请输入新的任务"></el-input>
              <div class="block" style="margin-top: 20px">
                <el-rate v-model="newEvaluation" :colors="colors"></el-rate>
              </div>
              <el-input style="margin-top: 20px" v-model="newRemark" placeholder="请输入新的备注"></el-input>
              <span slot="footer" class="dialog-footer">
                <el-button @click="taskDialogVisible = false">取 消</el-button>
                <el-button type="primary" @click="uploadModifyTask">确 定</el-button>
              </span>
            </el-dialog>
            <el-button
              style="margin-left: 10px;color: #67C23A"
              type="text"
              icon="el-icon-refresh"
              :disabled="identity"
              @click="modifyTask(scope.row.id, scope.row.task, scope.row.evaluation, scope.row.remark)"
            >修改</el-button>
          </template>
        </el-table-column>
      </el-table>
    </el-main>
  </el-container>
</template>
<script>
import { getRequest } from "../utils/api";
import { postRequest } from "../utils/api";
import { deleteRequest } from "../utils/api";
export default {
  mounted: function() {
    this.loading = true;
    this.loadTaskList();
  },
  data() {
    return {
      employeeid: "",
      loading: false,
      taskList: [],
      taskDialogVisible: false,
      newTask: "",
      newEvaluation: "",
      newRemark: "",
      newTaskid: "",
      identity: (this.$store.state.identity == 0)
    };
  },
  methods: {
    loadTaskList() {
      this.loading = true;
      var url = "http://localhost/backend/application/store/task_list.php";
      if (this.$store.state.employeeid == 0)
        url = "http://localhost/backend/application/store/task.php";
      getRequest(
        url,
        { identity: this.$store.state.identity },
        this.$store.state.token
      ).then(resp => {
        var json = resp.data;
        if (resp.status == 200 && json.status == 200) {
          this.taskList = json.data;
        } else {
          this.$alert("发生错误，获取任务列表失败！" + url);
        }
      });
      this.employeeid = '';
      this.loading = false;
    },
    getTaskListByEmpid() {
      if (this.employeeid == "") {
        this.loadTaskList();
        return;
      }
      this.loading = true;
      var url = "http://localhost/backend/application/store/task_list.php";
      getRequest(
        url,
        { identity: this.$store.state.identity, employeeid: this.employeeid },
        this.$store.state.token
      ).then(resp => {
        var json = resp.data;
        if (resp.status == 200 && json.status == 200) {
          this.taskList = json.data;
          this.$message.success("检索成功");
        } else {
          this.$message.error("检索失败");
        }
      });
      this.employeeid = '';
      this.loading = false;
    },
    deleteTask(taskid) {
      this.loading = true;
      var url = "http://localhost/backend/application/store/task.php";
      deleteRequest(url, { taskid: taskid }, this.$store.state.token).then(
        resp => {
          var json = resp.data;
          if (resp.status == 200 && json.status == 200) {
            this.$message.success("删除成功");
          } else {
            this.$message.error("删除失败");
          }
        }
      );

      clearTimeout(this.timer); //清除延迟执行

      this.timer = setTimeout(() => {
        //设置延迟执行
        location.reload();
      }, 500);
      this.loading = false;
    },
    modifyTask(id, task, evaluation, remark) {
      this.newTaskid = id;
      this.newTask = task;
      this.newEvaluation = evaluation;
      this.newRemark = remark;
      this.taskDialogVisible = true;
    },
    uploadModifyTask() {
      this.loading = true;
      var url = "http://localhost/backend/application/store/task.php";
      postRequest(
        url,
        {
          taskid: this.newTaskid,
          task: this.newTask,
          evaluation: this.newEvaluation,
          remark: this.newRemark
        },
        this.$store.state.token
      ).then(resp => {
        var json = resp.data;
        if (resp.status == 200 && json.status == 200) {
          this.$message.success("修改任务信息成功");
        } else {
          this.$message.error("修改任务信息失败");
        }
      });
      this.taskDialogVisible = false;
			clearTimeout(this.timer); //清除延迟执行

      this.timer = setTimeout(() => {
        //设置延迟执行
        location.reload();
      }, 500);
      this.loading = false;
    }
  }
};
</script>
<style>
.task_mana_header {
  background-color: #ececec;
  padding-left: 5px;
  display: flex;
  justify-content: flex-start;
}

.task_mana_main {
  display: flex;
  flex-direction: column;
  padding-left: 5px;
  background-color: #ececec;
}
</style>