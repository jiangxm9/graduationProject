<template>
  <div v-loading="loading">
    <div style="display: flex;justify-content: flex-start;flex-wrap: wrap;">
      <el-card
        style="width:330px;margin-top: 10px;margin-left: 30px"
        v-for="(food,index) in foods"
        :key="food.id"
        v-loading="cardloading[food.id]"
      >
        <div slot="header">
          <el-button
            style="float: right; padding: 3px 0;color: #ff0509"
            type="text"
            icon="el-icon-delete"
            :disabled="identity"
            @click="deleteFood(food.id)"
          >删除</el-button>
        </div>
        <div>
          <div>
            <img :src="food.icon" style="width: 70px;height: 70px" />
          </div>
          <div style="text-align: left;color:#20a0ff;font-size: 15px;margin-top: 13px">
            <span>菜名:</span>
            <span>{{food.name}}</span>
          </div>
          <div style="text-align: left;color:#20a0ff;font-size: 15px;margin-top: 13px">
            <span>价格:</span>
            <span>{{food.price}}</span>
          </div>
          <div style="text-align: left;color:#20a0ff;font-size: 15px;margin-top: 13px">
            <span>点单数量:</span>
            <el-input-number v-model="orderNum[index]" :min="0" :max="10"></el-input-number>
          </div>
          <div style="padding-top:10%">
            <el-dialog title="修改商品信息" :visible.sync="centerDialogVisible" width="30%" center>
              <div style="text-align:center;">
                <el-upload
                  class="avatar-uploader"
                  action="http://localhost/backend/application/store/food_icon.php"
                  :show-file-list="false"
                  :on-success="handleModifyFoodAvatarSuccess"
                  :before-upload="beforeAvatarUpload"
                >
                  <img v-if="newIcon" :src="newIcon" class="avatar" />
                  <i v-else class="el-icon-plus avatar-uploader-icon"></i>
                </el-upload>
              </div>
              <el-input v-model="newName" placeholder="请输入新的商品名称"></el-input>
              <el-input v-model="newPrice" placeholder="请输入新的商品价格"></el-input>
              <span slot="footer" class="dialog-footer">
                <el-button @click="centerDialogVisible = false">取 消</el-button>
                <el-button type="primary" @click="uploadModifiedFoodInfo">确 定</el-button>
              </span>
            </el-dialog>
            <el-button
              type="primary"
              :disabled="identity"
              round
              @click="modifyFoodInfo(food.id, food.name, food.price, food.icon)"
            >修改商品信息</el-button>
          </div>
        </div>
      </el-card>
    </div>
    <el-dialog title="添加商品" :visible.sync="addFoodDialogVisible" width="30%" center>
      <div style="text-align:center;">
        <el-upload
          class="avatar-uploader"
          action="http://localhost/backend/application/store/food_icon.php"
          :show-file-list="false"
          :on-success="handleAddFoodAvatarSuccess"
          :before-upload="beforeAvatarUpload"
        >
          <img v-if="addFoodIcon" :src="addFoodIcon" class="avatar" />
          <i v-else class="el-icon-plus avatar-uploader-icon"></i>
        </el-upload>
      </div>
      <el-input v-model="addFoodName" placeholder="请输入商品名称"></el-input>
      <el-input v-model="addFoodPrice" placeholder="请输入商品价格"></el-input>
      <span slot="footer" class="dialog-footer">
        <el-button @click="addFoodDialogVisible = false">取 消</el-button>
        <el-button type="primary" @click="uploadNewFoodInfo">确 定</el-button>
      </span>
    </el-dialog>
    <el-button
      type="primary"
      icon="el-icon-plus"
      :disabled="identity"
      round
      style="position: fixed;z-index:99;right:10px;top:100px;"
      @click="addFood"
    >添加商品</el-button>
    <div style="position: fixed;z-index:99;right:10px;top:200px; text-align: right; width: 180px">
      <el-input type="textarea" autosize placeholder="请输入订单备注" v-model="orderRemark"></el-input>
      <el-button
        type="primary"
        icon="el-icon-plus"
        round
        @click="addOrder"
        style="margin-top: 10px"
      >生成订单</el-button>
    </div>
  </div>
</template>
<script>
import { getRequest } from "../utils/api";
import { putRequest } from "../utils/api";
import { postRequest } from "../utils/api";
import { uploadFileRequest } from "../utils/api";
import { deleteRequest } from "../utils/api";
export default {
  mounted: function() {
    this.loading = true;
    this.loadFoodList();
    this.cardloading = new Array(50);
    this.cardloading.fill(false);
    this.orderNum = new Array(50);
    this.orderNum.fill(0);
  },
  data() {
    return {
      loading: false,
      cardloading: [],
      foods: [],
      orderNum: [],
      centerDialogVisible: false,
      newName: "",
      newPrice: "",
      newIcon: "",
      modifyFoodid: 0,
      addFoodDialogVisible: false,
      addFoodName: "",
      addFoodPrice: "",
      addFoodIcon: "",
      orderRemark: "",
      identity: (this.$store.state.identity == 0)
    };
  },
  methods: {
    loadFoodList() {
      this.loading = true;
      getRequest(
        "http://localhost/backend/application/store/food_list.php",
        { identity: this.$store.state.identity },
        this.$store.state.token
      ).then(resp => {
        var json = resp.data;
        if (resp.status == 200 && json.status == 200) {
          this.foods = json.data;
        } else {
          this.$alert("发生错误，获取商品列表失败！");
        }
        this.loading = false;
      });
    },
    modifyFoodInfo(id, name, price, icon) {
      this.newName = name;
      this.newPrice = price;
      this.newIcon = icon;
      this.modifyFoodid = id;
      this.centerDialogVisible = true;
    },
    addFood() {
      this.addFoodName = "";
      this.addFoodPrice = "";
      this.addFoodIcon = "";
      this.addFoodDialogVisible = true;
    },
    handleModifyFoodAvatarSuccess(res, file) {
      this.newIcon = URL.createObjectURL(file.raw);
    },
    handleAddFoodAvatarSuccess(res, file) {
      this.addFoodIcon = URL.createObjectURL(file.raw);
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
        "http://localhost/backend/application/store/food_icon.php",
        fd,
        this.$store.state.token
      ).then(resp => {
        var json = resp.data;
        //成功
        if (resp.status == 200 && json.status == 200) {
          this.$message.success("上传成功");
          this.newIcon = json.data.url;
          this.addFoodIcon = json.data.url;
          flag = true;
        } else {
          this.$message.error("上传失败");
          flag = false;
        }
      });
      return isJPG && isLt2M && flag;
    },
    uploadModifiedFoodInfo() {
      this.loading = true;
      postRequest(
        "http://localhost/backend/application/store/food.php",
        {
          foodid: this.modifyFoodid,
          foodname: this.newName,
          price: this.newPrice,
          imgurl: this.newIcon
        },
        this.$store.state.token
      ).then(resp => {
        var json = resp.data;
        if (resp.status == 200 && json.status == 200) {
          this.$message.success("修改成功");
        } else {
          this.$message.error("修改失败");
        }
        this.loading = false;
      });
      this.centerDialogVisible = false;
      location.reload();
    },
    uploadNewFoodInfo() {
      this.loading = true;
      putRequest(
        "http://localhost/backend/application/store/food.php",
        {
          foodname: this.addFoodName,
          price: this.addFoodPrice,
          imgurl: this.addFoodIcon
        },
        this.$store.state.token
      ).then(resp => {
        var json = resp.data;
        if (resp.status == 200 && json.status == 200) {
          this.$notify({
            title: '添加成功',
            message: '添加商品id为：' + json.data.id,
            type: 'success'
          });
        } else {
          this.$message.error("添加失败");
        }
        this.loading = false;
      });
      this.addFoodDialogVisible = false;
      location.reload();
    },
    deleteFood(id) {
      this.loading = true;
      deleteRequest(
        "http://localhost/backend/application/store/food.php",
        { foodid: id },
        this.$store.state.token
      ).then(resp => {
        var json = resp.data;
        if (resp.status == 200 && json.status == 200) {
          this.$message.success("删除成功");
        } else {
          this.$message.error("删除失败");
        }
        this.loading = false;
      });
      location.reload();
    },
    addOrder() {
      this.loading = true;
      var i;
      var orderContent = [];
      for (i in this.foods) {
        var item = {};
        if (this.orderNum[i] > 0) {
          item["id"] = this.foods[i].id;
          item["number"] = this.orderNum[i];
          orderContent.push(item);
        }
      }
      if(Object.keys(orderContent).length == 0){
        this.$notify.error({
            title: '下单失败',
            message: '请选择商品再下单'
          });
          this.loading = false;
          return;
      }
      var order = {};
      order["content"] = orderContent;
      order["remark"] = this.orderRemark;
      var orderJson = JSON.stringify(order);
      putRequest(
        "http://localhost/backend/application/store/order.php",
        {
          identity: this.$store.state.identity,
          order: orderJson
        },
        this.$store.state.token
      ).then(resp => {
        var json = resp.data;
        if (resp.status == 200 && json.status == 200) {
          this.$notify({
            title: '下单成功',
            message: '订单编号为：'+json.data.order_id+'  '+'下单时间：'+json.data.time,
            type: 'success',
            duration: 0
          });
        } else {
          this.$notify.error({
            title: '下单失败',
            message: '下单失败请再次尝试'
          });
        }
      });
      this.orderRemark = '';
      this.orderNum.fill(0);
      this.loading = false;
    }
  }
};
</script>