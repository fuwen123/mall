<!-- DetailHeader.vue -->
<template>
	<div class="ui-detail-header">
		<span class="iconfont" @click="$router.go(-1)">&#xe6ee;</span>
		<div class="navbar-wrapper">
			<div
				v-for="(item, key) in data"
				:key="key"
				v-bind:class="{ navbar_active: key == index }"
				v-on:click="changeEvent(key)"
			>
				{{ item.name }}
			</div>
		</div>
	</div>
</template>

<script>
import { header } from './static'
import { mapState, mapMutations } from 'vuex'
export default {
  data () {
    return {
      data: header
    }
  },

  computed: {
    ...mapState({
      index: state => state.pointsgoods.index
    })
  },

  created () {},

  methods: {
    ...mapMutations({
      changeIndex: 'changeIndex'
    }),

    changeEvent (index) {
      this.changeIndex(index)
    },

    goBack () {
      this.$router.go(-1)
    }
  }
}
</script>

<style lang="scss">
.ui-detail-header {
	padding: 0 0.45rem;
	background: rgba(255, 255, 255, 1);
	border-bottom: 1px solid rgba(232, 234, 237, 1);
	color: #55595f;
	font-size: 0.7rem;
	width: auto;
	display: flex;
	justify-content: center;
	align-content: center;
	align-items: center;
	flex-basis: auto;
	position: absolute;
	top: 0;
	left: 0;
	right: 0;
	.iconfont {
		width: 1.2rem;
		font-size:1.2rem;
		height: 1.2rem;
		line-height:1.2rem;
		font-size:0.7rem;
		cursor: pointer;
		position: absolute;
		left: 0.45rem;
		top: 0.5rem;
	}
	div.navbar-wrapper {
		line-height: 2.1rem;
		div {
			// line-height: 2.1rem;
			border-bottom: 0;
			display: inline-block;
			margin-right:2.4rem;
			color: #55595f;
			background-color: #fff;
			&.navbar_active {
				color: $primaryColor;
				border-bottom: 0.1rem solid $primaryColor;
			}
			&:last-child {
				margin-right: 0;
			}
			&:focus {
				outline: none;
			}
		}
	}
}
</style>
