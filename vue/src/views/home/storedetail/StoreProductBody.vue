<template>
	<div
		class="product-info"
		v-bind:class="{ border: showRightBorder }"
		@click="productClick"
	>
		<img
			class="product-icon"
			v-bind:src="item.goods_image_url"
		/>
		<span class="product-title">{{ item.goods_name }}</span>
		<div class="product-bottom">
			<span class="product-price">￥{{ item.goods_price }}</span>
			<span class="product-buy">{{ item.goods_salenum }}人已购买</span>
		</div>
	</div>
</template>

<script>
export default {
  name: 'StoreProductBody',
  data () {
    return {
      itemWidth: 0,
      itemHeight: 0,
      showRightBorder: this.index % 2 === 0
    }
  },
  props: ['item', 'index'],
  computed: {

  },
  methods: {
    productClick: function () {
      this.$router.push({ name: 'HomeGoodsdetail', query: { goods_id: this.item.goods_id } })
    }
  }
}
</script>

<style lang="scss" scoped>
.border {
	border-right: 1px solid #f5f5f5;
}
.product-info {
	display: flex;
	flex-direction: column;
	overflow: hidden;
	padding:0.5rem;
	justify-content: flex-end;
	border-bottom: 1px solid #f5f5f5;
	width:50%;box-sizing:border-box;
	.product-icon {
		align-self: center;
		width:100%;box-sizing:border-box;
	}
	.product-title {
		color: #333;
		font-size: .6rem;
		margin-top:1rem;
		overflow: hidden;
		display: -webkit-box;
		-webkit-box-orient: vertical;
		-webkit-line-clamp: 1;
	}
	.product-bottom {
		display: flex;
		justify-content: space-between;
		margin-top:.4rem;
		line-height:1rem;
		.product-price {
			color: $primaryColor;
			font-size:.7rem;
		}
		.product-buy {
			color: #999999;
			font-size:.65rem;
		}
	}
}
</style>
