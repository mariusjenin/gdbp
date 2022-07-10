
import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStreamReader;
import java.net.HttpURLConnection;
import java.net.MalformedURLException;
import java.net.ProtocolException;
import java.net.URL;
import java.util.Scanner;

/**
 * 
 * @author https://isbndb.com/
 * 
 */
public class TestReqISBN {
 
    private static HttpURLConnection con;
 
    public static void main(String[] args) throws MalformedURLException, ProtocolException, IOException {
    	
    	//Version propre
        //String url = "https://www.googleapis.com/books/v1/volumes";
        //String apiKey = "AIzaSyD3hQpgbknDC5IbXtR_GWn4Zj5Flo3jUSE";
        Scanner sc = new Scanner(System.in);
        String ISBN = sc.nextLine();
        sc.close();
    	
    	//Version sale
    	 String url = "https://www.googleapis.com/books/v1/volumes?q=" + ISBN + "+isbn&maxResults=1&fields=items/volumeInfo(title,authors,publisher,publishedDate,description,pageCount)";
 
        try {
 
            URL myurl = new URL(url);
            con = (HttpURLConnection) myurl.openConnection();
            //version propre
            //con.setRequestProperty("q", ISBN + "+isbn");
            //con.setRequestProperty("maxResults", 1);
            //con.setRequestProperty("key", apiKey);
            //con.setRequestProperty("fields", items/volumeInfo(title,authors,publisher,publishedDate,description,pageCount"));
           con.setRequestMethod("GET");
            StringBuilder content;
 
            try (BufferedReader in = new BufferedReader(new InputStreamReader(con.getInputStream()))) {
            	
                String line;
                content = new StringBuilder();
 
                while ((line = in.readLine()) != null) {
                    content.append(line);
                    content.append(System.lineSeparator());
                }
            }
 
            //System.out.println(content.toString());
            
            String titre="";
            String auteur="";
            String editeur="";
            String dateSortie="";
            String description="";
            int nbPages=0;

            String[] donnees = content.toString().split("\n");
            for(int i=0; i<donnees.length;i++) {
            	//donnees[i].trim();
            	if(donnees[i].contains("\"title\"")) {
            		donnees[i]=donnees[i].toString().split(":",2)[1];
            		donnees[i]=donnees[i].substring(2,donnees[i].length()-3);
            		titre=donnees[i];
            	}else if(donnees[i].contains("\"authors\"")){
            		donnees[i+1]=donnees[i+1].substring(6,donnees[i+1].length()-2);
            		auteur = donnees[i+1]; //A FAIRE MIEUX
            	}else if(donnees[i].contains("\"publisher\"")){
            		donnees[i]=donnees[i].toString().split(":",2)[1];
            		donnees[i]=donnees[i].substring(2,donnees[i].length()-3);
            		editeur=donnees[i];
            	}else if(donnees[i].contains("\"publishedDate\"")){
            		donnees[i]=donnees[i].toString().split(":",2)[1];
            		donnees[i]=donnees[i].substring(2,donnees[i].length()-3);
            		dateSortie=donnees[i];
            	}else if(donnees[i].contains("\"description\"")){
            		donnees[i]=donnees[i].toString().split(":",2)[1];
            		donnees[i]=donnees[i].substring(2,donnees[i].length()-3);
            		description=donnees[i];
            	}else if(donnees[i].contains("\"pageCount\"")){
            		donnees[i]=donnees[i].toString().split(":",2)[1];
            		donnees[i]=donnees[i].substring(1,donnees[i].length()-1);
            		nbPages=Integer.parseInt(donnees[i]);
            	}
            }
            System.out.println("Titre : " + titre);
            System.out.println("Auteur : " + auteur);
            System.out.println("Editeur : " + editeur);
            System.out.println("Date de publication : " + dateSortie);
            System.out.println("Description : " + description);
            System.out.println("Nombre de pages : " + nbPages);
 
        } finally {
 
            con.disconnect();
        }
    }
}