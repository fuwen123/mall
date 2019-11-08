<template>
	<div class="ui-cart-wrapper">
		<!-- header -->
		<v-cart-header
			ref="header"
			:issShowTabbar="type"
			:isEmpty="isEmpty"
		></v-cart-header>
		<div v-if="!isEmpty">
			<!-- list -->
			<v-cart-list
				ref="list"
				:issShowTabbar="type"
				:isCheckedAll="isFinish"
			></v-cart-list>
			<!-- footer -->
			<v-cart-footer
				:issShowTabbar="type"
				:isCheckedAll="isFinish"
				:isStatus="isStatus"
				:totalPrice="totalPrice"
				:totalAmount="totalAmount"
				:cartId="cartId"
			></v-cart-footer>
		</div>

		<div v-if="isEmpty" class="empty-cart">
			<p v-if="isOnline">您的购物车还是空的</p>
			<p v-if="!isOnline">登录后即可查看购物车中的商品</p>
			<span @click="goHome()" v-if="isOnline">随便逛逛</span>
			<span @click="goSingin()" v-if="!isOnline">去登录</span>
		</div>
	</div>
</template>

<script>
import cartHeader from './child/CartHeader'
import cartList from './child/CartList'
import cartFooter from './child/CartFooter'


import { mapState, mapMutations } from 'vuex'

export default {
  data () {
    return {
      totalPrice: 0,
      totalAmount: 0,
      cartId: '',
      type: 1, // 判断是否显示购物车底部的tabbar 1 ： 显示 0 不显示
      isFinish: false, // 编辑 false - 编辑~完成  true - 完成~编辑 false
      isStatus: true, // 是否默认选中底部的全选按钮
      isshowpromos: true, // 是否显示促销信息
      target: '', // 设置高度的element元素
      isEmpty: false
    }
  },

  computed: mapState({
    isOnline: state => state.member.isOnline
  }),

  created () {
    this.isSignin()

    // 监听是否改变购物车列表选中的状态
    this.$on('change-list-selected', data => {
      // data.isFinish ? 表示是完成状态，1，列表默认全不选中 2，促销信息不显示， 3，改变列表的高度  : 编辑状态  1，列表默认全选中 2，促销信息显示， 3，改变列表的高度
      if (!this.isEmpty) {
        this.isFinish = data.isFinish
        if (data.isFinish) {
          this.isshowpromos = false
          this.$refs.list.addChecked(false)
        } else {
          this.isshowpromos = true
          this.$refs.list.addChecked(true)
        }
        this.$refs.list.renderCart()
      }
    })

    // 监听是否更新购物车列表
    this.$on('update-cart-list', data => {
      if (data.isdelete) {
        this.$refs.list.deleteSelected()
      }
    })

    // 监听是否获取购物车促销信息
    this.$on('get-promos-data', data => {
      // this.$refs.promos.getCartPromos(data);
    })

	  // 监听选中的购物车
	  this.$on('calcu-cart-data', data => {
		  this.totalPrice = data.totalPrice
		  this.totalAmount = data.totalAmount
		  this.cartId = data.cartId
	  })

    // 监听购物车底部全选按钮是否选中
    this.$on('cart-bottom-status', data => {
      // data.isCheckAll ? true 1,显示促销信息， 2，重新计算列表的高度: false 1.隐藏促销信息， 2，重新计算列表的高度
      if (data.isCheckAll && !this.isFinish) {
        this.isshowpromos = true
      } else {
        this.isshowpromos = false
      }
      this.$refs.list.addChecked(data.isCheckAll)
      this.$refs.list.renderCart()
    })

    // 监听列表一个个选中底部全选按钮默认选中
    this.$on('change-footer-status', status => {
      this.isStatus = status
    })

    this.$on('list-is-empty', data => {
      if (data.length > 0) {
        this.isEmpty = false
      } else {
        this.isEmpty = true
      }
    })
  },

  mounted () {

  },

  components: {
    'v-cart-header': cartHeader,
    'v-cart-list': cartList,
    'v-cart-footer': cartFooter

  },

  methods: {

    /*
		 * isSignin: 是否登录
		 */
    isSignin () {
      if (this.isOnline) {
        this.isEmpty = false
      } else {
        this.isEmpty = true
      }
    },

    /*
		 * goHome: 跳转到首页
		 */
    goHome () {
      this.$router.push({ name: 'HomePointsgoods' })
    },

    goSingin () {
      this.$router.push({ name: 'HomeMemberLogin' })
    }
  }
}
</script>

<style lang="scss" scoped>
.ui-cart-wrapper {
}
.empty-cart {
	padding-top:6.2rem;
	text-align: center;
	img {
		width:3.7rem;
		height:3.7rem;
	}
	p {
		font-size:0.8rem;
		color: #333;
		line-height:1.1rem;
		padding: 1.3rem 0 2rem 0;
	}
	span {
		font-size:0.8rem;
		color: rgba(255, 255, 255, 1);
		height:2.2rem;
		background: $primaryColor;
		border-radius:0.15rem;
		line-height:2.2rem;
		display: inline-block;
		width:10rem;
	}
}
</style>
