<!-- PromotionPopup.vue -->
<template>
	<div v-if="detailInfo && detailInfo.promos && detailInfo.promos.length > 0">
		<mt-popup
			v-model="promoPopstatus"
			position="bottom"
			v-bind:close-on-click-modal="false"
		>
			<div class="detail-promotions">
				<div class="header">
					<h3>促销信息</h3>

					<span class="iconfont" v-on:click="close()">&#xe65a;</span>
				</div>
				<div class="promotions-body">
					<div
						class="body-list"
						v-for="(item, index) in detailInfo.promos"
						:key="index"
					>
						<span class="name">{{ item.name }}</span>
						<span class="title">{{ item.promo }}</span>
						<div class="content" v-if="item.desc">
							<p>{{ item.desc }}</p>
						</div>
					</div>
				</div>
			</div>
		</mt-popup>
	</div>
</template>

<script>
import { mapState, mapMutations } from 'vuex'
export default {
  data () {
    return {}
  },

  props: {
    promoPopstatus: {
      type: Boolean,
      default: false
    }
  },

  computed: {
    ...mapState({
      detailInfo: state => state.goodsdetail.detailInfo
    })
  },

  methods: {
    ...mapMutations({
      changePopstatus: 'changePopstatus'
    }),

    close () {
      this.changePopstatus(false)
    }
  }
}
</script>

<style lang="scss" scoped>
.detail-promotions {
	padding: 0 0.6rem;
	div.header {
		position: relative;
		h3 {
			font-size:0.75rem;
			color: rgba(78, 84, 93, 1);
			padding: 0;
			margin: 0;
			height:2.2rem;
			line-height:2.2rem;
			text-align: center;
			border-bottom: 0.5px solid rgba(232, 234, 237, 1);
			width: 100%;
		}
		img {
			position: absolute;
			top:0.7rem;
			right:0.5rem;
			width:0.8rem;
			height:0.8rem;
			opacity: 1;
		}
	}
	.promotions-body {
		background: rgba(255, 255, 255, 1);
		padding: 0 0 0.6rem 0;
		.body-list {
			margin-top: 0.6rem;
			span.name {
				background: rgba(255, 255, 255, 1);
				border-radius:0.1rem;
				font-size:0.5rem;
				color: $primaryColor;
				line-height:0.5rem;
				padding:0.15rem 0.3rem;
				display: inline-block;
				border: 1px solid $primaryColor;
				margin-right:0.35rem;
			}
			span.title {
				font-size:0.6rem;
				color: rgba(71, 76, 82, 1);
				line-height:0.6rem;
			}
			div.content {
				border-radius: 1px;
				padding:0.6rem 0 0 0;
				p {
					padding: 0;
					margin: 0;
					font-size:0.6rem;
					color: $primaryColor;
					line-height:0.8rem;
					display: -webkit-box;
					-webkit-box-orient: vertical;
					-webkit-line-clamp: 2;
					overflow: hidden;
					padding:0.6rem 0.6rem 0.5rem 0.6rem;
					background: rgba(255, 244, 244, 1);
				}
			}
		}
	}
}
</style>
