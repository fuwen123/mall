<!-- Details.vue -->
<template>
	<div class="show-iframe">

		<div class="list-empty" v-if="!detailInfo.pgoods_body">
			<p>本商品暂无详情</p>
		</div>
		<div class="mb-body" v-else v-html="detailInfo.pgoods_body">


		</div>

	</div>
</template>

<script>
import { mapState } from 'vuex'
export default {
  data () {
    return {}
  },
  computed: mapState({
    detailInfo: state => state.pointsgoods.detailInfo

  }),
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
	img {
		width: 2.75rem;
	}
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
