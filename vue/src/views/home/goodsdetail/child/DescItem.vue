<!-- Details.vue -->
<template>
	<div class="show-iframe">
		<div v-if="!mbBody.length">
			<div class="mb-body" v-if="detailInfo.goods_body">
				<div v-html="detailInfo.goods_body"></div>
			</div>
			<div class="list-empty" v-else>
				<p>本商品暂无详情</p>
			</div>
		</div>
		<div class="mb-body" v-else>
			<div v-for="(item,index) in mbBody" :key="index">
				<p v-if="item.type=='text'">{{item.value}}</p>
				<img v-else :src="item.value">
			</div>
		</div>
	</div>
</template>

<script>

export default {
  data () {
    return {}
  },
  computed: {
    getUrl: function () {
      return this.url
    }
  },
  props: ['mbBody', 'detailInfo'],
  methods: {}
}
</script>

<style lang="scss">
.show-iframe {
	height: 100%;
}

.list-empty {
	position: absolute;
	top: 50%;
	left: 50%;
	transform: translate(-50%, -50%);
	text-align: center;
	p {
		color: #7c7f88;
		font-size: 0.7rem;
		padding: 0;
		margin: 0;
		font-weight: normal;
	}
}
.mb-body {font-size:.7rem;
	img{max-width:100%}
}
</style>
