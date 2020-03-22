<template>
  <div v-loading="loading" calss="orderList" style="width: 100%">
    <el-table
      :data="orderList"
      ref="multipleTable"
      tooltip-effect="dark"
      border
      style="width: 100%"
    >
      <el-table-column prop="id" label="订单编号" width="180" align="center"></el-table-column>
      <el-table-column prop="time" label="下单时间" width="180" align="center"></el-table-column>
      <el-table-column prop="status" label="订单状态" align="center">
        <template slot-scope="scope">
          <el-switch
            v-model="scope.row.status"
            @change="changeOrderStatus(scope.row.id, scope.row.status)"
            active-text="订单未完成"
            inactive-text="订单已完成"
          ></el-switch>
        </template>
      </el-table-column>
      <el-table-column label="获取订单详情" align="center">
        <template slot-scope="scope">
          <el-dialog title="订单详情" :visible.sync="orderDetailDialogVisible" width="50%" center>
            <el-table :data="orderDetailContent" style="width: 100%">
              <el-table-column prop="id" label="商品编号" width="180" align="center"></el-table-column>
              <el-table-column prop="name" label="商品名称" width="180" align="center"></el-table-column>
              <el-table-column prop="icon" label="商品图片" align="center">
                <template slot-scope="scope">
                  <div style="text-align:center; width: 80%; height: 80%;">
                    <el-image
                      :fit="contain"
                      :src="scope.row.icon"
                      style="width: 100%; height: 100%;"
                    ></el-image>
                  </div>
                </template>
              </el-table-column>
              <el-table-column prop="price" label="商品价格" align="center"></el-table-column>
              <el-table-column prop="number" label="数量" align="center"></el-table-column>
            </el-table>
						<div style="font-size: 22px; padding-top: 10px">
						<span>订单备注:  {{orderDetailRemark}}</span>
						</div>
            <span slot="footer" class="dialog-footer">
              <el-button @click="orderDetailDialogVisible = false">取 消</el-button>
              <el-button type="primary" @click="orderDetailDialogVisible = false">确 定</el-button>
            </span>
          </el-dialog>
          <el-button type="primary" round @click="getOrderDetailByID(scope.row.id)">获取详情</el-button>
        </template>
      </el-table-column>
    </el-table>
  </div>
</template>
<script>
import { getRequest } from "../utils/api";
import { postRequest } from "../utils/api";
export default {
  mounted: function() {
    this.loading = true;
    this.loadOrderList();
  },
  data() {
    return {
      loading: false,
      orderList: [],
      orderDetailContent: [],
      orderDetailRemark: "",
      orderDetailDialogVisible: false
    };
  },
  methods: {
    loadOrderList() {
      this.loading = true;
      getRequest(
        "http://localhost/backend/application/store/order_list.php",
        { identity: this.$store.state.identity, count: 20 },
        this.$store.state.token
      ).then(resp => {
        var json = resp.data;
        if (resp.status == 200 && json.status == 200) {
          this.orderList = json.data;
        } else {
          this.$alert("发生错误，获取订单列表失败！");
        }
      });
      this.loading = false;
    },
    changeOrderStatus(orderid, newStatus) {
      this.loading = true;
      var temp = 0;
      if (newStatus) temp = 1;
      postRequest(
        "http://localhost/backend/application/store/order.php",
        {
          identity: this.$store.state.identity,
          id: orderid,
          status: temp
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
    },
    getOrderDetailByID(orderid) {
      this.loading = false;
      getRequest(
        "http://localhost/backend/application/store/order.php",
        { id: orderid, identity: this.$store.state.identity },
        this.$store.state.token
      ).then(resp => {
        var json = resp.data;
        if (resp.status == 200 && json.status == 200) {
					this.orderDetailContent = json.data.content;
					this.orderDetailRemark = json.data.remark;
        } else {
          this.$alert("发生错误，获取订单详情失败！");
        }
      });
      this.loading = false;
      this.orderDetailDialogVisible = true;
    }
  }
};
</script>
<style>
.orderList {
  display: flex;
  flex-direction: column;
  padding-left: 5px;
  background-color: #ececec;
  margin-top: 20px;
  padding-top: 10px;
}
</style>