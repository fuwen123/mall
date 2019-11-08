<template>
	<div class="product-list" :style="{backgroundColor:editablePageConfig.editable_page_config_content.back_color,paddingTop:editablePageConfig.editable_page_config_content.margin_top,paddingBottom:editablePageConfig.editable_page_config_content.margin_bottom}">
		<div class="product-list-header">
			<div class="header-icon"></div>
			<span class="header-title" v-if="editablePageConfig.editable_page_config_content.text[0].list[0]">{{ editablePageConfig.editable_page_config_content.text[0].list[0].content }}</span>
			<span class="iconfont"></span>
		</div>
		<div class="product-list-body">
			<div
					v-for="(item, index) in editablePageConfig.goods_list[0]"
					v-bind:key="index"
					class="product-info"
					v-bind:class="{ border: index % 2 == 0 }"
					@click="productClick(item.goods_id)"
			>
				<img class="product-icon" v-lazy="item.goods_image_url"/>
				<span class="product-title">{{ item.goods_name }}</span>
				<div class="product-bottom">
					<span class="product-price">￥{{ item.goods_promotion_price }}</span>
					<span class="product-buy">{{ item.goods_salenum }}人已购买</span>
				</div>
			</div>
		</div>
	</div>
</template>

<script>

export default {
  name: 'Model6',
  data () {
    return {}
  },
  props: ['editablePageConfig'],
  components: {
  },
  computed: {

  },
  methods: {
	  productClick(goods_id) {
		  this.$router.push({ name: 'HomeGoodsdetail', query: { goods_id: goods_id } })
	  }
  }
}
</script>

<style lang="scss" scoped>
.product-list {
	background: #ffffff;
	margin-top:0.6rem;
	margin-bottom:0.6rem;
	.product-list-header {
		background: #fff;
		display: flex;
		height:2rem;
		padding: 0 0.25rem;
		justify-content: space-between;
		align-items: center;
		border-bottom: 1px solid #f5f5f5;
		.header-title {
			font-size:0.7rem;
			color: #333;
		}
		.header-icon {
			width:1rem;
			height:1rem;
		}
	}
	.product-list-body {
		background: #fff;
		display: flex;
		flex-wrap: wrap;
	}
}
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
		margin-top: 1rem;
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
