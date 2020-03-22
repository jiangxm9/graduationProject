<template>
  <el-form class="login-container" label-position="left" label-width="0px" v-loading="loading">
    <h3 class="login_title">登录</h3>
    <el-form-item prop="account">
      <el-input type="text" v-model="loginForm.username" auto-complete="off" placeholder="请输入账号"></el-input>
    </el-form-item>
    <el-form-item prop="checkPass">
      <el-input
        type="password"
        v-model="loginForm.password"
        auto-complete="off"
        placeholder="请输入密码"
        show-password
      ></el-input>
    </el-form-item>
    <el-form-item prop="chooseIdentity">
      <el-radio v-model="radio" label="0">店员</el-radio>
      <el-radio v-model="radio" label="1">店主</el-radio>
    </el-form-item>
    <el-form-item style="width: 100%">
      <el-button type="primary" @click.native.prevent="submitClick" style="width: 100%">登录</el-button>
    </el-form-item>
    <el-form-item>
      <el-link type="info" href="/register">register now!</el-link>
    </el-form-item>
  </el-form>
</template>
<script>
import { postRequest } from "../utils/api";
import { getRequest } from "../utils/api";
export default {
  data() {
    return {
      radio: "0",
      loginForm: {
        username: "",
        password: ""
      },
      loading: false
    };
  },
  methods: {
    submitClick: function() {
      var _this = this;
      this.loading = true;
      console.log(_this.radio);
      if (_this.radio == 1) {
        //本地测试时所用链接，部署到服务器上需要再次修改
        postRequest(
          "http://localhost/backend/application/res_login.php",
          {
            username: this.loginForm.username,
            password: this.loginForm.password
          },
          this.$store.state.token
        ).then(resp => {
          _this.loading = false;
          var json = resp.data;
          //成功
          if (json.status == 200) {
            this.$store.commit("setIdentity", this.radio);
            var token = json.data.token;
            this.$store.commit("setToken", token);
            this.$store.commit("setEmployeeid", 0);

            getRequest(
              "http://localhost/backend/application/store/info.php",
              { identity: this.$store.state.identity },
              this.$store.state.token
            ).then(resp => {
              var json = resp.data;
              console.log(json);
              if (json.status == 200) {
                this.$store.commit("setRestaurantName", json.data.name);
                this.$store.commit("setRestaurantIcon", json.data.icon);

                window.location.href = "/restaurant";
              } else{
                this.$alert("发生错误，获取店铺信息失败！");
              }
            });
          }
          //失败
          else if (json.status == 400) {
            _this.$alert("发生错误，登录失败！");
          } else if (json.status == 403) {
            _this.$alert("用户名或密码错误，登录失败！");
          }
        });
      } else if (_this.radio == 0) {
        postRequest(
          "http://localhost/backend/application/emp_login.php",
          {
            username: this.loginForm.username,
            password: this.loginForm.password
          },
          this.$store.state.token
        ).then(resp => {
          _this.loading = false;
          var json = resp.data;
          //成功
          if (json.status == 200) {
            this.$store.commit("setIdentity", this.radio);
            var token = json.data.token;
            var employeeid = json.data.id;
            this.$store.commit("setToken", token);
            this.$store.commit("setEmployeeid", employeeid);

            window.location.href = "/employeeid";
          }
          //失败
          else if (json.status == 400) {
            _this.$alert("发生错误，登录失败！");
          } else if (json.status == 403) {
            _this.$alert("用户名或密码错误，登录失败！");
          }
        });
      }
    }
  }
};
</script>
<style>
.login-container {
  border-radius: 15px;
  background-clip: padding-box;
  margin: 180px auto;
  width: 350px;
  padding: 35px 35px 15px 35px;
  background: #fff;
  border: 1px solid #eaeaea;
  box-shadow: 0 0 25px #cac6c6;
}

.login_title {
  margin: 0px auto 40px auto;
  text-align: center;
  color: #505458;
}

.chooseIdentity {
  margin: 0px 0px 35px 0px;
  text-align: left;
}
</style>
