<template>
  <nb-container>
    <nb-header>
      <nb-left>
        <nb-button transparent>
          <nb-icon name="arrow-back" :on-press="goBack" />
        </nb-button>
      </nb-left>
      <nb-body>
        <nb-title>Details</nb-title>
      </nb-body>
      <nb-right />
    </nb-header>
    <nb-content>
      <nb-card
        :style="{
          marginLeft: 20,
          marginRight: 20,
          marginTop: 20,
          marginBottom: 20,
        }">
    <nb-card-item rounded :style="{ backgroundColor: '#62B1F6' }">
          <nb-body>
            <nb-grid class="marginBottom">
              <nb-col>
                <nb-text class="headerText">Kenmerk Ref</nb-text>
                <nb-text class="detailText">12345678</nb-text>
              </nb-col>
              <nb-col>
                <nb-text class="headerText">Percentage</nb-text>
                <nb-text class="detailText">12345678</nb-text>
              </nb-col>
            </nb-grid>
            <nb-grid class="marginBottom">
              <nb-col>
                <nb-text class="headerText">Incasseerder</nb-text>
                <nb-text class="detailText">12345678</nb-text>
              </nb-col>
              <nb-col>
                <nb-text class="headerText">Schuld</nb-text>
                <nb-text class="detailText">$585.89</nb-text>
              </nb-col>
            </nb-grid>
          </nb-body>
        </nb-card-item>
      </nb-card>
    </nb-content>
  </nb-container>
</template>

<script>
export default {
  props: {
    navigation: {
      type: Object,
    },
  },
  data() {
    return {
      dataIsReady: false,
    };
  },
  methods: {
    goBack: function () {
      this.navigation.goBack();
    },
    // dismissModal: function () {
    //   this.isModalVisible = false;
    // },
  },
};
</script>
