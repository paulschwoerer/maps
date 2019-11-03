<template>
  <div class="map-click-popup">
    <template v-if="showFavoriteScreen">
      <h2 class="map-click-popup-title">{{ t("maps", "New Favorite") }}</h2>

      <form action="" @submit.prevent="handleNewFavoriteSubmit">
        <input type="text" v-model="newFavoriteName" />
        <input type="text" v-model="newFavoriteCategory" />
        <textarea v-model="newFavoriteComment"></textarea>
      </form>
    </template>
    <template v-else>
      <h2 class="map-click-popup-title">{{ t("maps", "This Place") }}</h2>

      <textarea
        name="map-click-popup-address"
        class="map-click-popup-address"
        cols="30"
        rows="10"
      ></textarea>

      <Actions>
        <ActionButton
          :title="t('maps', 'Add to favorites')"
          icon="icon-favorite"
        />
        <ActionButton
          :title="t('maps', 'Add contact address')"
          icon="icon-user"
        />
      </Actions>
    </template>
  </div>
</template>

<script>
import Actions from "@nextcloud/vue/dist/Components/Actions";
import ActionButton from "@nextcloud/vue/dist/Components/ActionButton";
import ActionInput from "@nextcloud/vue/dist/Components/ActionInput";
import { MAP_NAMESPACE } from "../../store/modules/map";
import { mapState } from "vuex";
import MapMode from "../../data/enum/MapMode";

export default {
  name: "MapClickPopup",

  data() {
    return {
      newFavoriteName: "",
      newFavoriteCategory: "Personal", // TODO: get default category name
      newFavoriteComment: ""
    };
  },

  computed: {
    ...mapState({
      mapMode: state => state[MAP_NAMESPACE].mode
    }),
    showFavoriteScreen() {
      return this.mapMode === MapMode.ADDING_FAVORITES;
    }
  },

  methods: {
    handleNewFavoriteSubmit() {}
  },

  components: {
    ActionButton,
    Actions,
    ActionInput
  }
};
</script>

<style scoped lang="scss">
.map-click-popup {
  .map-click-popup-title {
    white-space: nowrap;
  }

  .map-click-popup-address {
    border: 0;
  }
}
</style>
