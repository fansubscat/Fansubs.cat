package cat.fansubs.app.serveraccess;

import com.google.gson.FieldNamingPolicy;
import com.google.gson.Gson;
import com.google.gson.GsonBuilder;

import java.io.IOException;
import java.util.List;
import java.util.concurrent.TimeUnit;

import cat.fansubs.app.BuildConfig;
import cat.fansubs.app.beans.Fansub;
import cat.fansubs.app.beans.News;
import cat.fansubs.app.serveraccess.model.base.ServerError;
import cat.fansubs.app.serveraccess.model.base.ServerResponse;
import cat.fansubs.app.utils.HttpLoggingInterceptor;
import cat.fansubs.app.utils.UserAgentHeaderInterceptor;
import okhttp3.OkHttpClient;
import retrofit2.Response;
import retrofit2.Retrofit;
import retrofit2.converter.gson.GsonConverterFactory;

public class ServerAccess {
    private static final String ACCESS_URL = "http://api.fansubs.cat/";

    public static final String STATUS_OK = "ok";
    public static final String STATUS_KO = "ko";

    public static final String ERROR_SERVER_ERROR = "ERROR_SERVER_ERROR";
    public static final String ERROR_SERVER_TIMEOUT = "ERROR_SERVER_TIMEOUT";

    public static ServerResponse<Fansub> getFansubs() {
        Response<ServerResponse<Fansub>> response;
        try {
            response = getBackendService().getFansubs().execute();
        } catch (IOException e) {
            response = null;
        }
        return getResponseBodyOrErrorIfFailed(response);
    }

    public static ServerResponse<News> getNews(int pageNumber, String search, List<String> fansubIds) {
        Response<ServerResponse<News>> response;
        try {
            response = getBackendService().getNews(pageNumber, search, fansubIds).execute();
        } catch (IOException e) {
            response = null;
        }
        return getResponseBodyOrErrorIfFailed(response);
    }

    private static <T> ServerResponse<T> getResponseBodyOrErrorIfFailed(Response<ServerResponse<T>> response) {
        if (response != null && response.isSuccessful()) {
            ServerResponse<T> result = response.body();
            if (result == null) {
                result = createServerError(response);
            }
            return result;
        } else {
            return createServerError(response);
        }
    }

    private static <T> ServerResponse<T> createServerError(Response response) {
        ServerResponse<T> simulatedResponse = new ServerResponse<>();
        simulatedResponse.setStatus(STATUS_KO);
        simulatedResponse.setError(getErrorFromStatusCode(response));
        return simulatedResponse;
    }

    private static ServerError getErrorFromStatusCode(Response response) {
        ServerError serverError = new ServerError();
        if (response != null) {
            serverError.setCode(ERROR_SERVER_ERROR);
            serverError.setDescription("Server error, got HTTP status code " + response.code());
        } else {
            serverError.setCode(ERROR_SERVER_TIMEOUT);
            serverError.setDescription("Server connection timed out");
        }
        return serverError;
    }

    private static FansubsService getBackendService() {
        OkHttpClient.Builder builder = new OkHttpClient.Builder()
                .connectTimeout(5, TimeUnit.SECONDS)
                .readTimeout(30, TimeUnit.SECONDS)
                .addInterceptor(new UserAgentHeaderInterceptor());
        if (BuildConfig.DEBUG) {
            builder.addInterceptor(new HttpLoggingInterceptor());
        }

        GsonBuilder gsonBuilder = new GsonBuilder();
        gsonBuilder.setFieldNamingPolicy(FieldNamingPolicy.LOWER_CASE_WITH_UNDERSCORES);
        Gson gson = gsonBuilder.create();

        Retrofit retrofit = new Retrofit.Builder()
                .baseUrl(ACCESS_URL)
                .client(builder.build())
                .addConverterFactory(GsonConverterFactory.create(gson))
                .build();

        return retrofit.create(FansubsService.class);
    }
}
