export const isPublicShare = () => {
  return document.body.id === "body-public";
};

export const getCurrentPublicShareToken = () => {
  // FIXME: there must be a better way to retrieve the token client side
  const path = location.pathname.split("/");

  return path[path.length - 1];
};

/**
 * Perform API request
 *
 * TODO: Use axios or similar instead of jQuery ajax
 *
 * @param url : string
 * @param method : string
 * @param data : {} | null
 * @returns {Promise<{}>}
 */
export const apiRequest = (url, method = "GET", data = null) => {
  return new Promise((resolve, reject) => {
    $.ajax({
      url: OC.generateUrl(url),
      type: method,
      data,
      async: true
    })
      .done(resolve)
      .fail(reject);
  });
};

/**
 * Show temporary notification
 *
 * TODO: Use non-deprecated function
 *
 * @param message : string
 */
export const showNotification = message => {
  OC.Notification.showTemporary(t("maps", message));
};
