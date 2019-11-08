<template>
	<div class="container">
		<!-- header -->
		<div class="common-header-wrap">
			<mt-header title="举报详情" class="common-header">
				<mt-button slot="left" icon="back" @click="$router.go(-1)"></mt-button>
			</mt-header>
		</div>
<div v-if="!inform_id">
		<!-- body -->
		<div class="body">
			<div @click="typeVisible=true">
				<mt-cell title="举报类型" is-link :value="type_name" />
			</div>
			<div @click="subjectVisible=true">
				<mt-cell title="举报主题" is-link :value="subject_name" />
			</div>
			<mt-cell class="menu-item" title="举报凭证" ></mt-cell>
			<div class="image-wrapper mt-10">
				<div class="user-avatar iconfont icon-xiangji" ref="upload_btn1" @change="uploadInformPic(0,$event)">
					<img v-if="image[0]" :src="image[0]">
					<input type="file" accept="image/jpg,image/png,image/gif,image/bmp,image/jpeg">
				</div>
				<div class="user-avatar iconfont icon-xiangji" ref="upload_btn2" @change="uploadInformPic(1,$event)">
					<img v-if="image[1]" :src="image[1]">
					<input type="file" accept="image/jpg,image/png,image/gif,image/bmp,image/jpeg">
				</div>
				<div class="user-avatar iconfont icon-xiangji" ref="upload_btn3" @change="uploadInformPic(2,$event)">
					<img v-if="image[2]" :src="image[2]">
					<input type="file" accept="image/jpg,image/png,image/gif,image/bmp,image/jpeg">
				</div>
			</div>
			<div class="order-comment-body">

				<div class="enter">
					<textarea
						placeholder="举报内容"
						v-model="content"
					></textarea>
				</div>
			</div>
		</div>
		<mt-button type="primary" @click='addInform' class="ds-button-large">提交</mt-button>
	<!--举报类型-->
	<mt-popup v-model="typeVisible" position="right" class="common-popup-wrapper">
		<div class="common-header-wrap">
			<mt-header title="举报类型" class="common-header">
				<mt-button slot="left" icon="back" @click="typeVisible=false"></mt-button>
			</mt-header>
		</div>
		<div class="common-popup-content">
			<mt-radio
					v-model="type"
					:options="type_options">
			</mt-radio>
		</div>
	</mt-popup>
		<!--举报主题-->
		<mt-popup v-model="subjectVisible" position="right" class="common-popup-wrapper">
			<div class="common-header-wrap">
				<mt-header title="举报主题" class="common-header">
					<mt-button slot="left" icon="back" @click="subjectVisible=false"></mt-button>
				</mt-header>
			</div>
			<div class="common-popup-content">
				<mt-radio v-model="subject" :options="subject_options"></mt-radio>
			</div>
		</mt-popup>
	</div>
		<div v-else>
			<!--举报信息-->
			<div>
				<div class="menu-content">
					<mt-cell title="举报商家" :value="goods_info.store_name"></mt-cell>
					<mt-cell title="举报商品" :value="goods_info.goods_name"></mt-cell>
					<mt-cell title="举报类型" :value="subject_info.informsubject_type_name"></mt-cell>
					<mt-cell title="举报主题" :value="subject_info.informsubject_content"></mt-cell>
					<mt-cell title="举报时间" :value="$moment.unix(inform.inform_datetime).format('YYYY.MM.DD')"></mt-cell>
					<mt-cell title="举报内容" :value="inform.inform_content"></mt-cell>
					<div @click="isshow=true" v-if="inform_pic.length"><mt-cell title="举报凭证" value="查看"></mt-cell></div>
					<mt-cell title="举报状态" :value="inform.inform_handle_type_text"></mt-cell>
					<mt-cell title="处理信息" v-if="inform.inform_handle_message" :value="inform.inform_handle_message"></mt-cell>
				</div>
				<mt-popup v-model="isshow" popup-transition="popup-fade" v-if="inform_pic.length>0">
					<div class="preview-picture">
						<div class="picture-header" v-on:click="isshow=false">
						<span>关闭</span><span v-if="inform_pic">{{ defaultindex + 1 }} / {{ inform_pic.length }}</span>
						</div>

						<div class="picture-body">
							<mt-swipe
									:auto="0"
									:show-indicators="true"
									:default-index="defaultindex"
									class="ui-common-swiper"
									:prevent="false"
									:stop-propagation="true"
									@change="handleChange"
							>
								<mt-swipe-item v-for="(item, index) in inform_pic" v-bind:key="index">
									<img v-bind:src="item" />
								</mt-swipe-item>
							</mt-swipe>
						</div>
					</div>
				</mt-popup>
			</div>
		</div>
	</div>
</template>

<script>
import { Toast } from 'mint-ui'
import { addInform, uploadInformPic, getCommonData, getInformInfo, getInformSubject } from '../../../api/memberInform'

export default {
  data () {
    return {
      content: '',
      inform_id: this.$route.query.inform_id,
      goods_id: this.$route.query.goods_id,
      subjectVisible: false,
      subject_options: [],
      subject_name: '',
      subject: '',
      typeVisible: false,
      type_options: [],
      type_name: '',
      type: '',
      image: ['', '', ''],
      file_value: ['', '', ''],
      inform: {},
      inform_pic: [],
      isshow: false,
      defaultindex: 0,
      subject_info: false,
      goods_info: false
    }
  },
  created () {
    if (!this.inform_id) {
      getCommonData(this.goods_id).then(res => {
        let type_options = res.result.type_list

        for (var i in type_options) {
          this.type_options.push({
            label: type_options[i].informtype_name,
            value: type_options[i].informtype_id + ',' + type_options[i].informtype_name
          })
        }
        this.type = type_options[0].informtype_id + ',' + type_options[0].informtype_name
      }).catch(function (error) {
        Toast(error.message)
      })
    } else {
      getInformInfo(this.inform_id).then(res => {
        this.inform = res.result.inform_info
        this.subject_info = res.result.subject_info
        this.inform_pic = res.result.inform_pic
        this.goods_info = res.result.goods_info
      }).catch(function (error) {
        Toast(error.message)
      })
    }
  },
  watch: {
    subject: function (subject) {
      let temp = subject.split(',')
      this.subject_name = temp[1]
      this.subjectVisible = false
    },
    type: function (type) {
      let temp = type.split(',')
      this.type_name = temp[1]
      this.typeVisible = false
      getInformSubject(temp[0]).then(res => {
        let subject_options = res.result.inform_subject_list
        this.subject_options = []
        for (var i in subject_options) {
          this.subject_options.push({
            label: subject_options[i].informsubject_content,
            value: subject_options[i].informsubject_id + ',' + subject_options[i].informsubject_content
          })
        }
        this.subject = subject_options[0].informsubject_id + ',' + subject_options[0].informsubject_content
      }).catch(function (error) {
        Toast(error.message)
      })
    }
  },
  methods: {
    handleChange (index) {
      this.defaultindex = index
    },
    addInform () {
      addInform(this.goods_id, this.subject, this.content, this.file_value).then(res => {
        this.$router.push({ name: 'MemberInformList' })
      }).catch(function (error) {
        Toast(error.message)
      })
    },
    uploadInformPic (index, event) {
      if (typeof (event.target.files[0]) === 'undefined') {
        return
      }
      let formdata = new FormData()

      formdata.append('inform_pic', event.target.files[0])

      uploadInformPic(formdata).then(res => {
        this.image.splice(index, 1, res.result.pic + '?' + Math.floor(Math.random() * 100))
        this.file_value.splice(index, 1, res.result.file_name)
      }).catch(function (error) {
        Toast(error.message)
      })
    }

  }
}
</script>

<style lang="scss" scoped>
	.common-score-wrapper .back{display: block}
.container {
	display: flex;
	flex-direction: column;
	justify-content: flex-start;
	align-items: stretch;

	.body {
		top: 2.2rem;
		width: 100%;
		.order-comment-body {
			background: rgba(255, 255, 255, 1);
			padding:0.75rem;
			.body-list {
				display: flex;
				justify-content: left;
				align-content: center;
				align-items: center;
				padding-bottom:0.75rem;
				border-bottom: 1px solid #e8eaed;
			}
			.image {
				width:3.7rem;
				height:3.7rem;
				flex-shrink: 0;
				img {
					width: 100%;
					height: 100%;
				}
			}
			.comment {
				flex-basis: 100%;
				padding-left:0.75rem;
				span {
					font-size:0.8rem;
					color: #7c7f88;
					text-align: left;
					display: block;
				}
				ul {
					display: flex;
					justify-content: space-between;
					align-content: center;
					align-items: center;
					margin-top:1.2rem;
					li {
						img {
							width:1rem;
							height:1rem;
							flex-shrink: 0;
						}
						label {
							font-size:0.7rem;
							color: rgba(78, 84, 93, 1);
							font-weight: normal;
						}
					}
				}
			}
			.enter {
				padding-top:0.75rem;
				textarea {
					width: 100%;
					height: 6rem;
					background: rgba(247, 249, 250, 1);
					border: 1px solid #f7f9fa;
					box-sizing: border-box;
					padding: 0.5rem 0 0 0.5rem;
					font-size:0.7rem;
					-webkit-appearance: none;
					outline: none;
				}
			}
		}
	}
}
	.image-wrapper{display:flex}
	.user-avatar {
		flex:1;
		border:1px solid #eee;
		position: relative;
		width: 5rem;
		height: 5rem;
		margin: 0 auto;
		text-align: center;
		img {
			width: 100%;
			height: 100%
		}
		input {
			width: 100%;
			height: 100%;
			position: absolute;
			top: 0;
			left: 0;
			opacity: 0
		}
	}
	.user-avatar::before {
		position: absolute;
		font-size: 1.5rem;
		line-height: 5rem;
		left: 50%;
		margin-left: -.75rem;
		color: rgba(0, 0, 0, 0.5)
	}
	.swipe-wrapper {
		width: 100%;
	}
	.mint-popup {
		width: 100%;
		height: 100%;
		background-color: #000;
	}
	.mint-swipe,
	.mint-swipe-items-wrap {
		position: static;
	}
	.preview-picture {
		width: 100%;
		height: 100%;
		position: fixed;
		z-index: 10;
		top: 0;
		bottom: 0;
		left: 0;
		right: 0;
		background-color: #000;
		.picture-header {
			height:2.2rem;
			color: #000;
			background-color: #fff;
			display: flex;
			justify-content: center;
			align-content: center;
			align-items: center;
			width: 100%;
			top: 0;
			span {
				font-size:0.7rem;
				font-weight: normal;
				&:first-child {
					cursor: pointer;
					position: absolute;
					left:0.75rem;
					background-size:1.2rem;
					display: inline-block;
					height:2.2rem;
					line-height:2.2rem;
				}
			}
		}
		.picture-body {
			position: absolute;
			top:2.2rem;
			bottom: 0;
			width: 100%;
			display: flex;
			justify-content: center;
			align-content: center;
			align-items: center;
		}
	}
	.menu-item{background:#f7f9fa}

</style>
<style>
	.menu-content .mint-cell-title{flex:unset;width:4rem}
	.menu-content .mint-cell-value{flex:1}
</style>
