import AppMode from "../../data/enum/MapMode";
import { publicAPIRequest, showNotification } from "../../utils/common";
import { getCategoryRawName } from "../../utils/mapUtils";

export const PUBLIC_FAVORITES_NAMESPACE = "publicFavorites";

const state = {
  favorites: [],
  appMode: AppMode.DEFAULT
};

const getters = {
  mappedByCategory(state) {
    if (state.favorites.length === 0) {
      return {};
    }

    return {
      [getCategoryRawName(state.favorites[0].category)]: state.favorites
    };
  }
};

const actions = {
  getFavorites({ commit }) {
    publicAPIRequest("favorites", "GET")
      .then(data => {
        commit("setFavorites", data);
      })
      .catch(() =>
        showNotification(t("maps", "Failed to share favorites category"))
      );
  },
  addFavorite({}) {}
};

const mutations = {
  setFavorites(state, favorites) {
    state.favorites = favorites;
  },
  setAppMode(state, mode) {
    state.appMode = mode;
  }
};

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations
};
