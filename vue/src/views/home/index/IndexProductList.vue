<template>
	<div class="product-list">
		<div class="product-list-header" @click="productListClick">
			<div class="header-icon"></div>
			<span class="header-title">{{ title }}</span>
			<span class="iconfont">&#xe650;</span>
		</div>
		<div class="product-list-body">
			<index-product-body
				:item="item"
				:index="index"
				v-for="(item, index) in items"
				v-bind:key="index"
			></index-product-body>
		</div>
	</div>
</template>

<script>
import IndexProductBody from './IndexProductBody'

export default {
  name: 'IndexProductList',
  data () {
    return {}
  },
  props: ['items', 'title', 'type'],
  components: {
    IndexProductBody
  },
  computed: {

  },
  methods: {
    productListClick: function () {
      this.$router.push({
        name: 'HomeGoodslist',
        query: { sort_key: this.type }
      })
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
