<template>
  <div class="search-wrapper">
    <search-header ref='header' :keyword="keywords"></search-header>
    <div class="search-body">

      <div class="list current-search" v-if='currenKeywords.length > 0'>
        <div class="list-header">
          <span>最近搜索</span>
          <img src="../../../assets/image/home/home-search-delete1.png" v-on:click='deleteCurrent()'>
        </div>
        <ul>
          <li class="item" v-for="(item, index) in currenKeywords" v-on:click='getKey(item)' :key="index">{{item}}</li>
        </ul>
      </div>

        <div class="list hot-wrapper" v-if="hotKeywords[0] !== ''">
        <div class="list-header">
          <span>热门搜索</span>
        </div>
        <ul>
          <li class="item" v-for='(item, index) in hotKeywords' :key='index' v-on:click='getKey(item)'>{{ item}}</li>
        </ul>
      </div>
    </div>
  </div>
</template>

<script>
import { mapState, mapMutations } from 'vuex'
import searchHeader from './SearchHeader'
import { searchKeywordList } from '../../../api/homesearch'
export default {
  data () {
    return {
      hotKeywords: [],
      currenKeywords: [],
      keywords: this.$store.state.homesearch.currentKey ? this.$store.state.homesearch.currentKey : ''
    }
  },
  components: {
    searchHeader
  },
  computed: {
    ...mapState({
      currentKey: state => state.homesearch.currentKey
    })
  },

  created () {
    this.getHotKeywords()
    this.getCurrentKey()
  },

  methods: {
    ...mapMutations({
      'changeKey': 'changeKey'
    }),

    getHotKeywords () {
      searchKeywordList().then(res => {
        this.hotKeywords = Object.assign([], res.result.list, this.hotKeywords)
      })
    },

    getKey (item) {
      if (item.content) {
        this.keywords = item.content
      } else {
        this.keywords = item
      }
      this.changeKey(this.keywords)
      this.$refs.header.search(this.keywords)
    },

    deleteCurrent () {
      this.utils.save('keyword', [])
      this.currenKeywords = this.utils.fetch('keyword')
    },

    getCurrentKey () {
      this.currenKeywords = this.utils.fetch('keyword')
    }
  }
}
</script>

<style lang='scss' scoped>
	.search-wrapper{
		height: auto;
		width: auto;
		background-color: #F0F2F5;
		.search-body {
			padding: 0.75rem;
			div.list {
				margin-bottom: 1.5rem;
				.list-header {
					display: flex;
		    		justify-content: space-between;
		    		align-content: center;
		    		align-items: center;
					span {
						color: #333;
						font-size: 0.7rem;
						background: url('../../../assets/image/home/home-search-history.png') no-repeat left center;
						background-size: 0.8rem;
		    			padding-left: 1.25rem;
		    			align-self: flex-end;
					}
					img {
						width: 1.2rem;
						height: 1.2rem;
						cursor: pointer;
					}
				}
				ul {
		    		display: flex;
		    		padding-top: 0.65rem;
		    		flex-wrap: wrap;
		    		li {
		    			padding: 0.4rem;
		    			background-color: #fff;
		    			color: #333;
		    			font-size: 0.6rem;
		    			margin-right: 0.8rem;
		    			margin-bottom: 0.8rem;
		    			cursor: pointer;
		    		}
		    	}
			}
			div.hot-wrapper {
				.list-header{
					span{
						background: url('../../../assets/image/home/home-search-hot.png') no-repeat left center;
						background-size: 0.8rem;
					}
				}
			}
		}
	}
</style>
