<template>
  <el-container class="store_container">
    <el-header>
      <div class="store_title">饭店管理平台</div>
    </el-header>
    <el-container>
      <el-col style="width: 15%">
        <el-menu class="el-menu-vertical">
          <el-menu-item @click="skip('foodmanagement')">
            <template slot="title">
              <i class="el-icon-menu"></i>
              <span>商品管理</span>
            </template>
          </el-menu-item>
          <el-menu-item @click="skip('ordermanagement')">
            <i class="el-icon-menu"></i>
            <span slot="title">订单管理</span>
          </el-menu-item>
          <el-menu-item @click="skip('employeemanagement')">
            <i class="el-icon-menu"></i>
            <span slot="title">员工管理</span>
          </el-menu-item>
          <el-menu-item @click="skip('taskmanagement')">
            <i class="el-icon-menu"></i>
            <span slot="title">任务管理</span>
          </el-menu-item>
        </el-menu>
        <div class="store_info">
          <div style="text-align:center; width: 80%; height: 200px; padding-left: 10%">
            <el-image :fit="contain" :src="imgSrc" style="width: 100%; height: 100%;"></el-image>
          </div>
          <div style="padding-top: 10%">
            <span style="font-size: 22px; display: inline;" v-text="restaurantName"></span>
          </div>
          <div style="padding-top:10%">
            <el-dialog title="修改店铺信息" :visible.sync="centerDialogVisible" width="30%" center>
              <div style="text-align:center;">
                <el-upload
                  class="avatar-uploader"
                  action="http://localhost/backend/application/store/icon.php"
                  :show-file-list="false"
                  :on-success="handleAvatarSuccess"
                  :before-upload="beforeAvatarUpload"
                >
                  <img v-if="imageUrl" :src="imageUrl" class="avatar" />
                  <i v-else class="el-icon-plus avatar-uploader-icon"></i>
                </el-upload>
              </div>
              <el-input v-model="inputResName" placeholder="请输入新的店铺名称"></el-input>
              <span slot="footer" class="dialog-footer">
                <el-button @click="centerDialogVisible = false">取 消</el-button>
                <el-button type="primary" @click="uploadNewResInfo">确 定</el-button>
              </span>
            </el-dialog>
            <el-button type="primary" round @click="centerDialogVisible=true">修改店铺信息</el-button>
          </div>
        </div>
      </el-col>
      <el-container style="width: 85%">
        <router-view></router-view>
      </el-container>
    </el-container>
  </el-container>
</template>
<script>
import { uploadFileRequest } from "../utils/api";
import { postRequest } from "../utils/api";
export default {
  data() {
    return {
      restaurantName: this.$store.state.restaurantName,
      imgSrc: this.$store.state.restaurantIcon,
      centerDialogVisible: false,
      imageUrl: this.$store.state.restaurantIcon,
      inputResName: this.$store.state.restaurantName
    };
  },
  methods: {
    skip(path) {
      this.$router.push({name: path});
    },
    handleAvatarSuccess(res, file) {
      this.imageUrl = URL.createObjectURL(file.raw);
    },
    beforeAvatarUpload(file) {
      const isJPG = file.type === "image/jpeg";
      const isLt2M = file.size / 1024 / 1024 < 2;
      var flag = false;

      if (!isJPG) {
        this.$message.error("上传头像图片只能是 JPG 格式!");
        return isJPG;
      }
      if (!isLt2M) {
        this.$message.error("上传头像图片大小不能超过 2MB!");
        return isLt2M;
      }
      let fd = new FormData();
      fd.append("file", file);
      uploadFileRequest(
        "http://localhost/backend/application/store/icon.php",
        fd,
        this.$store.state.token
      ).then(resp => {
        var json = resp.data;
        //成功
        if (json.status == 200) {
          this.$message.success("上传成功");
          this.$store.commit("setRestaurantIcon", json.data.url);
          this.imageUrl = json.data.url;
          flag = true;
        } else {
          this.$message.error("上传失败");
          flag = false;
        }
      });
      return isJPG && isLt2M && flag;
    },
    uploadNewResInfo() {
      this.imgSrc = this.$store.state.restaurantIcon;
      var tempName = this.inputResName;
      this.$store.commit("setRestaurantName", tempName);
      postRequest(
        "http://localhost/backend/application/store/info.php",
        { name: tempName },
        this.$store.state.token
      ).then(resp =>{
        var json = resp.data;
        if(json.status == 200){
          this.$message.success("修改成功");
        } else{
          this.$message.error("修改失败");
        }
      })
      this.restaurantName = this.$store.state.restaurantName;
      this.centerDialogVisible = false;
      location.reload();
    }
  }
};
</script>
<style>
.store_container {
  height: 100%;
  position: absolute;
  top: 0px;
  left: 0px;
  width: 100%;
}
.el-header {
  background-color: #20a0ff;
  color: #333;
  text-align: center;
  display: flex;
  align-items: center;
  justify-content: space-between;
}
.el-col {
  background-color: #ececec;
}
.store_title {
  color: rgb(10, 10, 10);
  font-size: 22px;
  display: inline;
}
.store_info {
  padding-top: 10%;
  height: 100%;
  width: 100%;
}
.avatar-uploader .el-upload {
  border: 1px dashed #d9d9d9;
  border-radius: 6px;
  cursor: pointer;
  position: relative;
  overflow: hidden;
}
.avatar-uploader .el-upload:hover {
  border-color: #409EFF;
}
.avatar-uploader-icon {
  font-size: 28px;
  color: #8c939d;
  width: 178px;
  height: 178px;
  line-height: 178px;
  text-align: center;
}
.avatar {
  width: 178px;
  height: 178px;
  display: block;
}
</style>