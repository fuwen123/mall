<!-- Detailinfo.vue -->
<template>
	<div v-if="detailInfo">
		<div v-if="detailInfo.bargain_info" class="bargain-wrapper">

			<div class="bargain-content" @click="bargainVisible=true">
				<div class="title">砍价购</div>
				<div class="content">
					<div>距结束仅剩</div>
					<div><span v-if="day"><em>{{day}}</em>天</span><span><em>{{hours}}</em>时</span><span><em>{{minute}}</em>分</span><span><em>{{second}}</em>秒</span></div>
				</div>
				<div class="arrow iconfont">&#xe650;</div>
			</div>
			<mt-popup v-model="bargainVisible" position="bottom" class="bargain-popup-wrapper">
				<div class="mt">玩法详情</div>
				<div class="mc">
					<div class="line">
						1.活动期间消费者可邀请好友帮忙砍价，好友砍到底价后，即可按底价购买商品；
					</div>
					<div class="line">
						2.好友帮忙砍到底价后，消费者须在有效期内购买砍价商品，逾期商品将恢复原价；
					</div>
					<div class="line">
						3.同一商品，同一用户仅可享受一次优惠价格；
					</div>
					<div class="line">
						4.同一商品，同一用户仅能帮好友砍价一次；
					</div>
					<div class="line">
						5.砍价商品数量有限，商品售罄后，您将无法购买。
					</div>
				</div>
			</mt-popup>
		</div>
		<div class="ui-detail-info">

			<div class="info-header ui-flex">
				<h3>{{ detailInfo.goods_name }}</h3>
				<div class="favorite">
					<i class="iconfont" v-on:click="productUnlike()" v-if="isFavorate">&#xe6d5;</i>
					<i class="iconfont" v-on:click="productLike()" v-else>&#xe6d6;</i>
					<span>{{favoriteName}}</span>
				</div>
			</div>

			<div class="price">
			<span class="current-price">￥<slot v-if="detailInfo.bargain_info">{{ detailInfo.bargain_info.bargain_floorprice }}</slot><slot v-else-if="detailInfo.pintuan_type==1">{{ detailInfo.pintuan_price }}</slot><slot v-else-if="detailInfo.promotion_type">{{ detailInfo.promotion_price }}</slot><slot v-else>{{ detailInfo.goods_price }}</slot>
			</span>
				<span class="old-price">￥{{ detailInfo.goods_marketprice }}</span>
			</div>

			<div class="info-sub ui-flex" v-if="detailInfo.goods_advword">
				<p>{{ detailInfo.goods_advword }}</p>
			</div>
			<div v-if="!detailInfo.bargain_info && detailInfo.pintuan_info==''">
				<div class="info-promotions" v-if="detailInfo.promotion_type=='groupbuy'">
					<div class="left">
						<i>抢购</i>
						<span v-if="detailInfo.upper_limit">最多可购买{{detailInfo.upper_limit}}件</span>
					</div>
					<span class="right">{{fromNow(detailInfo.promotion_end_time)}}结束</span>
				</div>
				<div class="info-promotions" v-if="detailInfo.promotion_type=='xianshi'">
					<div class="left">
						<i>限时</i>
						<span v-if="detailInfo.lower_limit">最低{{detailInfo.lower_limit}}件起</span>
					</div>
					<span class="right">{{fromNow(detailInfo.promotion_end_time)}}结束</span>
				</div>
			</div>
		</div>
	</div>

</template>

<script>
import { mapState, mapMutations } from 'vuex'
import { productLike, productUnlike } from '../../../../api/homegoodsdetail'

export default {
  data () {
    return {
      bargainVisible: false,
      flag: false,
      day: 0,
      hours: 0,
      minute: 0,
      second: 0,
      time: false,
      orderTime: '', // 下单时间
      arrivalsTime: '', // 到达时间
      arrivalsTitle: '', // 到达时间的标题
      arrivalsRange: '' // 到达时间区间,
    }
  },
  computed: {
    ...mapState({
      isFavorate: state => state.goodsdetail.isFavorate,
      detailInfo: state => state.goodsdetail.detailInfo,
      currentProductId: state => state.goodsdetail.currentProductId,
      user: state => state.member.info
    }),
    favoriteName () {
      return this.isFavorate ? '已关注' : '关注'
    }
  },
  created () {
  },
  mounted () {
    this.time = setInterval(() => {
      if (this.flag == true) {
        clearInterval(this.time)
      } else {
        this.timeDown()
      }
    }, 1000)
  },
  methods: {
    ...mapMutations({
      saveInfo: 'saveDetailInfo',
      changeIndex: 'changeIndex',
      saveIsFavorate: 'saveIsFavorate'
    }),
    fromNow (time) {
      return this.$moment(time * 1000).fromNow()
    },
    /*
     * getCommentStatus： 去到评论页面
     */
    getCommentStatus () {
      this.changeIndex(2)
    },
    /*
     * productLike： 收藏商品
     */
    productLike () {
      if (this.user) {
        let id = this.detailInfo.goods_id
        productLike(id).then(res => {
          if (res) {
            this.saveIsFavorate(true)
          }
        })
      } else {
        this.$router.push({ name: 'HomeMemberLogin' })
      }
    },
    /*
     * productUnlike： 取消收藏
     */
    productUnlike () {
      if (this.user) {
        let id = this.detailInfo.goods_id
        productUnlike(id).then(res => {
          if (res) {
            this.saveIsFavorate(false)
          }
        })
      } else {
        this.$router.push({ name: 'HomeMemberLogin' })
      }
    },

    /*
	 * timeDown: 倒计时
	 */
    timeDown () {
      let end_time = false
      if (this.detailInfo.bargain_info) {
        end_time = this.detailInfo.bargain_info.bargain_endtime
      }
      if (end_time) {
        const endTime = new Date(end_time * 1000)
        const nowTime = new Date()
        let leftTime = parseInt((endTime.getTime() - nowTime.getTime()) / 1000)
        this.day = parseInt(leftTime / (24 * 60 * 60))
        this.hours = this.formate(parseInt((leftTime / (60 * 60)) % 24))
        this.minute = this.formate(parseInt((leftTime / 60) % 60))
        this.second = this.formate(parseInt(leftTime % 60))
        if (leftTime <= 0) {
          this.flag = true
          this.detailInfo.bargain_info = ''
          this.saveInfo(this.detailInfo)
        }
      }
    },
    /*
     * 格式化时间
     */
    formate (time) {
      if (time >= 10) {
        return time
      } else {
        return `0${time}`
      }
    }
  }
}
</script>

<style lang="scss" scoped>
	.bargain-wrapper{
		.bargain-content{
			padding:.5rem;color:#fff;
			background: #EA3F64;background-image: linear-gradient(to right, #ff5e00, #ea3f3f);
			display: flex;
			.title{flex:1;font-size:1rem;line-height: 1.2rem;}
			.content{width:auto;font-size:.6rem;line-height: .6rem;
				em{margin:0 .1rem}
			}
			.arrow{width:1rem;text-align: right;line-height: 1.2rem;}

		}
		.bargain-popup-wrapper{padding:.5rem;color:#333;box-sizing: border-box;padding-bottom: 3rem;
			.mt{font-size:.8rem;text-align: center}
			.mc{font-size:.6rem;
				.line{margin-top:.5rem;}
			}
		}
	}
.ui-detail-info {
	padding: 0 0.75rem;
	background: #ffffff;
	.ui-flex {
		display: flex;
		/*justify-content: space-between;*/
		align-content: center;
		align-items: center;
		flex-basis: 100%;
		width: auto;
	}
	.info-header {
		padding: 0.4rem 0;
		h3 {
			color: #333;
			font-size: 0.7rem;
			padding: 0;
			margin: 0;
			font-weight: normal;
			line-height:1.2rem;
			height:2.4rem;
			width:85%;
			overflow:hidden;
		}
		.favorite {
			height:2.4rem;
			text-align:center;
			i{width:2.4rem;display:block;font-size:1.2rem;line-height:1.2rem;color:#e93b3d;}
			span{font-size:0.5rem;line-height:1rem;height:1rem;display:block;}
		}
	}

	.price {
		padding-bottom: 0.4rem;
		display: flex;
		span {
			display: block;
			font-weight: normal;
			&.current-price {
				font-size: 0.7rem;
				color: $mainColor;
				line-height: 1rem;
			}
			&.old-price {
				font-size: 0.6rem;
				color: rgba(164, 170, 179, 1);
				line-height: 1rem;
				text-decoration: line-through;
				margin-left: 0.3rem;
			}
		}
	}

	.info-sub {
		border-bottom: 0.5px solid #e8eaed;
		padding-bottom: 0.25rem;
		p {
			padding: 0;
			margin: 0;
			color: $mainColor;
			font-size: 0.5rem;
		}
	}

	.info-promotions {
		display: flex;
		justify-content: flex-start;
		align-content: center;
		align-items: center;
		padding: 0.75rem 0;
		border-bottom: 0.5px solid #e8eaed;
		line-height:1.5rem;
		span {
			margin-left: 0.75rem;
			font-size: 0.6rem;
			color: rgba(143, 142, 148, 1);
		}
		img {
			width: 30.4rem;
		}
	}
	.info-promotions i{font-size:.7rem;border:1px solid $mainColor;color:$mainColor;padding:0.1rem 0.3rem;border-radius:0.1rem;}
	.info-promotions .left{flex:1}
	.info-promotions .right{width:4rem;text-align: right}
}
</style>
