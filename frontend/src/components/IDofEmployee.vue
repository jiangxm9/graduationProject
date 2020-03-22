<template>
  <el-form class="id-container" label-position="left" label-width="0px">
    <h3 class="id_title">员工编号</h3>
    <el-form-item prop="id">
      <p>您的员工编号为:</p>
      <div>{{ employeeid }}</div>
    </el-form-item>
    <el-form-item prop="skip">
      <el-button type="info" @click="getResInfo">我已加入饭店</el-button>
    </el-form-item>
  </el-form>
</template>
<script>
import { getRequest } from "../utils/api";
export default {
  data() {
    return {
      employeeid: this.$store.state.employeeid
    };
  },
  methods: {
    getResInfo() {
      getRequest(
        "http://localhost/backend/application/store/info.php",
        { identity: this.$store.state.identity },
        this.$store.state.token
      ).then(resp => {
        var json = resp.data;
        if (json.status == 200) {
          console.log(json.data.name + json.data.icon);
          this.$store.commit("setRestaurantName", json.data.name);
          this.$store.commit("setRestaurantIcon", json.data.icon);
          window.location.href = "/restaurant";
        } else {
          this.$alert("发生错误，获取店铺信息失败！");
        }
      });
    }
  }
};
</script>
<style>
.id-container {
  border-radius: 15px;
  background-clip: padding-box;
  margin: 180px auto;
  width: 350px;
  padding: 35px 35px 15px 35px;
  background: #fff;
  border: 1px solid #eaeaea;
  box-shadow: 0 0 25px #cac6c6;
}

.id_title {
  margin: 0px auto 40px auto;
  text-align: center;
  color: #505458;
}
</style>