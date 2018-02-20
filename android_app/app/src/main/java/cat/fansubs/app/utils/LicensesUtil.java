package cat.fansubs.app.utils;

import android.content.res.XmlResourceParser;

import org.xmlpull.v1.XmlPullParser;
import org.xmlpull.v1.XmlPullParserException;
import org.xmlpull.v1.XmlPullParserFactory;

import java.io.IOException;
import java.util.ArrayList;
import java.util.Collections;
import java.util.Comparator;
import java.util.List;

import cat.fansubs.app.FansubsApplication;
import cat.fansubs.app.beans.License;

public class LicensesUtil {
    private static final String TAG_CHILD = "license";
    private static final String ATTR_NAME = "name";
    private static final String ATTR_COPYRIGHT = "copyright";
    private static final String ATTR_TEXT = "text";
    private static final String ATTR_URL = "url";

    public static List<License> loadLicenses() {
        try {
            XmlPullParser parser = XmlPullParserFactory.newInstance().newPullParser();
            parser.setInput(FansubsApplication.getInstance().getAssets().open("licenses.xml"), null);

            List<License> licenses = new ArrayList<>();
            int event = parser.getEventType();

            License currentLicense = new License();

            String currentTag = null;

            while (event != XmlResourceParser.END_DOCUMENT) {
                event = parser.next();

                if (event == XmlPullParser.START_TAG) {
                    currentTag = parser.getName();
                } else if (event == XmlPullParser.END_TAG) {
                    if (parser.getName().equals(TAG_CHILD)) {
                        licenses.add(currentLicense);
                        currentLicense = new License();
                    }
                } else if (event == XmlPullParser.TEXT) {
                    String text = parser.getText();
                    if (!text.trim().equals("")) {
                        if (ATTR_NAME.equals(currentTag)) {
                            currentLicense.setName(trimAllLines(text));
                        } else if (ATTR_COPYRIGHT.equals(currentTag)) {
                            currentLicense.setCopyright(trimAllLines(text));
                        } else if (ATTR_URL.equals(currentTag)) {
                            currentLicense.setUrl(trimAllLines(text));
                        } else if (ATTR_TEXT.equals(currentTag)) {
                            currentLicense.setLicense(trimAllLines(text));
                        }
                    }
                }
            }

            Collections.sort(licenses, new Comparator<License>() {
                @Override
                public int compare(License lhs, License rhs) {
                    return lhs.getName().compareToIgnoreCase(rhs.getName());
                }
            });

            return licenses;
        } catch (XmlPullParserException | IOException e) {
            throw new RuntimeException(e);
        }
    }

    private static String trimAllLines(String text) {
        StringBuilder result = null;
        String[] lines = text.replace("\r\n", "\n").trim().split("\\n");
        for (String line : lines) {
            if (result == null) {
                result = new StringBuilder(line.trim());
            } else {
                result.append("\n").append(line.trim());
            }
        }

        return result != null ? result.toString() : "";
    }
}
