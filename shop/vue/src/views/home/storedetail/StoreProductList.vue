<template>
	<div class="product-list">
		<div class="product-list-header">
			<span class="header-title">店铺推荐</span>
		</div>
		<div class="product-list-body">
			<store-product-body
				:item="item"
				:index="index"
				v-for="(item, index) in items"
				v-bind:key="index"
			></store-product-body>
		</div>
	</div>
</template>

<script>
import StoreProductBody from './StoreProductBody'

export default {
  name: 'StoreProductList',
  data () {
    return {}
  },
  props: ['items', 'title', 'type'],
  components: {
    StoreProductBody
  },
  computed: {

  },
  methods: {
    productListClick: function () {
      this.$router.push({
        name: 'StoreGoodslist',
        query: { sort_key: this.type }
      })
    }
  }
}
</script>

<style lang="scss" scoped>
.product-list {
	background: #ffffff;
	margin-top: 0.6rem;
	margin-bottom: 0.6rem;
	.product-list-header {
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
		display: flex;
		flex-wrap: wrap;
	}
}
</style>
