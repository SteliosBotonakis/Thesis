package mypackage;

import java.io.ByteArrayOutputStream;
import java.io.FileInputStream;
import java.io.IOException;
import java.io.InputStream;
import java.lang.reflect.Member;
import java.text.ParseException;
import java.text.SimpleDateFormat;
import java.util.Calendar;
import java.util.Date;
import java.util.HashSet;
import java.util.Set;

import org.apache.jena.datatypes.xsd.XSDDateTime;
import org.apache.jena.graph.Graph;
import org.apache.jena.ontology.Individual;
import org.apache.jena.ontology.OntModel;
import org.apache.jena.query.Query;
import org.apache.jena.query.QueryExecution;
import org.apache.jena.query.QueryExecutionFactory;
import org.apache.jena.query.QueryFactory;
import org.apache.jena.query.QuerySolution;
import org.apache.jena.query.ResultSet;
import org.apache.jena.query.ResultSetFormatter;
import org.apache.jena.rdf.model.InfModel;
import org.apache.jena.rdf.model.Model;
import org.apache.jena.rdf.model.ModelFactory;
import org.apache.jena.rdf.model.Property;
import org.apache.jena.rdf.model.RDFNode;
import org.apache.jena.rdf.model.ResIterator;
import org.apache.jena.rdf.model.Resource;
import org.apache.jena.rdf.model.SimpleSelector;
import org.apache.jena.rdf.model.Statement;
import org.apache.jena.rdf.model.StmtIterator;
import org.apache.jena.reasoner.Reasoner;
import org.apache.jena.reasoner.ValidityReport;
import org.apache.jena.reasoner.ValidityReport.Report;
import org.apache.jena.vocabulary.RDF;
import org.codehaus.jackson.node.ArrayNode;
import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;
import org.springframework.web.bind.annotation.GetMapping;
import org.springframework.web.bind.annotation.PostMapping;
import org.springframework.web.bind.annotation.RequestBody;
import org.springframework.web.bind.annotation.RequestParam;
import org.springframework.web.bind.annotation.ResponseBody;
import org.springframework.web.bind.annotation.RestController;

import com.mashape.unirest.http.HttpResponse;
import com.mashape.unirest.http.JsonNode;
import com.mashape.unirest.http.Unirest;
import com.mashape.unirest.http.exceptions.UnirestException;

import javassist.bytecode.Descriptor.Iterator;
import openllet.jena.PelletReasonerFactory;
import openllet.query.sparqldl.jena.SparqlDLExecutionFactory;
import virtuoso.jena.driver.VirtGraph;
import virtuoso.jena.driver.VirtModel;
import virtuoso.jena.driver.VirtuosoQueryExecution;
import virtuoso.jena.driver.VirtuosoQueryExecutionFactory;
import virtuoso.jena.driver.VirtuosoUpdateFactory;
import virtuoso.jena.driver.VirtuosoUpdateRequest;
@RestController
public class Controller {
	
	public static final String TOURISM_ONTOLOGY = "./ontologies/tourism.owl";
	public static final String WEATHER_ONTOLOGY = "./ontologies/weather.owl";

	// DOMAIN DATASETS
	public static final String TOURISM_DATASET = "./ontologies/tourism-dataset.owl";
	public static final String WEATHER_DATASET = "./ontologies/weather-dataset.owl";
	Reasoner reasoner = PelletReasonerFactory.theInstance().create();
	VirtGraph vg = new VirtGraph("http://147.27.60.65/sensorOntology", "jdbc:virtuoso://147.27.60.65:1111", "dba", "boto");
	Query sparql = QueryFactory.create("PREFIX sosa: <http://www.w3.org/ns/sosa/>\r\n" + 
	 		"PREFIX owl: <http://www.w3.org/2002/07/owl#>\r\n" + 
	 		"PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>" +
	 		"CONSTRUCT FROM <http://147.27.60.65/sensorOntology> WHERE {?s ?p ?o}");


	QueryExecution vqe = VirtuosoQueryExecutionFactory.create (sparql, vg);

	Model model1 = vqe.execConstruct();
     //Graph g = model1.getGraph();
     
     
    InfModel infModel = ModelFactory.createInfModel( reasoner, model1 );
	
	@GetMapping("/select")
	@ResponseBody
	public String query(@RequestParam String thequery) throws UnirestException {
	

//		Reasoner reasoner = PelletReasonerFactory.theInstance().create();
//    		VirtGraph vg = new VirtGraph("http://147.27.60.65/sensorOntology", "jdbc:virtuoso://147.27.60.65:1111", "dba", "boto");
//    		Query sparql = QueryFactory.create("PREFIX sosa: <http://www.w3.org/ns/sosa/>\r\n" + 
//    		 		"PREFIX owl: <http://www.w3.org/2002/07/owl#>\r\n" + 
//    		 		"PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>" +
//    		 		"CONSTRUCT FROM <http://147.27.60.65/sensorOntology3> WHERE {?s ?p ?o}");
//        
//        
//    		QueryExecution vqe = VirtuosoQueryExecutionFactory.create (sparql, vg);
//
//    		Model model1 = vqe.execConstruct();
// 	        Graph g = model1.getGraph();
// 	       System.out.println(g.size());
// 	        
// 	        
//	        InfModel infModel = ModelFactory.createInfModel( reasoner, model1 );
	        Query sparql1 = QueryFactory.create(thequery);
	        ByteArrayOutputStream b = new ByteArrayOutputStream();
	        QueryExecution qe = SparqlDLExecutionFactory.create(sparql1, infModel);
	        ResultSet results = qe.execSelect();
    		ResultSetFormatter.outputAsJSON(b, results);
//    		System.out.print(jsonResult);
//			while (results.hasNext()) {
//				QuerySolution result = results.nextSolution();
//			    RDFNode s = result.get("s");
//			    RDFNode p = result.get("p");
//			    RDFNode o = result.get("o");
//			    
//			    
////			    map.put("s", s.toString());
////			    map.put("p", p.toString());
////			    map.put("o", o.toString());
//
//			    
//			    System.out.println(" { " + s + " " + p + " " + o + "}");
//			}
	        
	        qe.close();
			return b.toString();
//		    return jsonResult;
		
	}
	
	@GetMapping("/create")
	@ResponseBody
	public void create() throws UnirestException {
		Reasoner reasoner = PelletReasonerFactory.theInstance().create();
		VirtGraph vg = new VirtGraph("http://147.27.60.65/sensorOntology4", "jdbc:virtuoso://147.27.60.65:1111", "dba", "boto");
//		Query sparql = QueryFactory.create("CONSTRUCT FROM <http://147.27.60.65/sensorOntology> WHERE {?s ?p ?o}");
//	    
//	    
//		QueryExecution vqe = VirtuosoQueryExecutionFactory.create (sparql, vg);
//		Model model = vqe.execConstruct();
//	    InfModel infModel = ModelFactory.createInfModel( reasoner, model );
//	    
//	    System.out.println(model.size() +" "+ infModel.size() +" "+vg.getCount()+" "+vg.size()+" "+vg.getBatchSize()+" "+vg.getFetchSize());
//	    
//	    Resource rs = model.getResource("http://www.w3.org/ns/sosa/Home");
	    //Resource s = model.createResource("http://example.org/data/Home1", rs);
	    //Resource s = model.getResource("http://example.org/data/Room145TemperatureObservation");
	    //System.out.println(model.containsResource(s));
		 //Statement stmt1 = model.createStatement(s, p, o);
		//model.add(s, RDF.type, "http://www.w3.org/ns/sosa/Home");
	    
	    
		for(int i=900; i<=999; i++)
		{
			String j = Integer.toString(i); 
			
			System.out.println(j);
			
			String str1 = "INSERT INTO GRAPH <http://147.27.60.65/sensorOntology4> { <http://example.org/data/Home"+i+"> <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> <http://www.w3.org/2002/07/owl#NamedIndividual> }";
			VirtuosoUpdateRequest vur1 = VirtuosoUpdateFactory.create(str1, vg);
			vur1.exec();  
			
		    String str2 = "INSERT INTO GRAPH <http://147.27.60.65/sensorOntology4> { <http://example.org/data/Home"+i+"> <http://www.w3.org/2003/01/geo/wgs84_pos#location> <http://example.org/data/Athens> }";
		    VirtuosoUpdateRequest vur2 = VirtuosoUpdateFactory.create(str2, vg);
		    vur2.exec();  
		     
		    String str3 = "INSERT INTO GRAPH <http://147.27.60.65/sensorOntology4> { <http://example.org/data/Home"+i+"> <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> <http://www.w3.org/ns/sosa/Home> }";
		    VirtuosoUpdateRequest vur3 = VirtuosoUpdateFactory.create(str3, vg);
		    vur3.exec();   
		    
		    String str4 = "INSERT INTO GRAPH <http://147.27.60.65/sensorOntology4> { <http://example.org/data/Home"+i+"> <http://www.w3.org/ns/ssn/hasProperty> <http://example.org/data/Home"+i+"Temperature> }";
		    VirtuosoUpdateRequest vur4 = VirtuosoUpdateFactory.create(str4, vg);
		    vur4.exec();   
		    
		    String str5 = "INSERT INTO GRAPH <http://147.27.60.65/sensorOntology4> { <http://example.org/data/Home"+i+"> <http://www.w3.org/ns/ssn/hasProperty> <http://example.org/data/Home"+i+"Humidity> }";
		    VirtuosoUpdateRequest vur5 = VirtuosoUpdateFactory.create(str5, vg);
		    vur5.exec();  
		    
		    String str6 = "INSERT INTO GRAPH <http://147.27.60.65/sensorOntology4> { <http://example.org/data/Home"+i+"> <http://www.w3.org/ns/ssn/hasProperty> <http://example.org/data/Home"+i+"Luminocity> }";
		    VirtuosoUpdateRequest vur6 = VirtuosoUpdateFactory.create(str6, vg);
		    vur6.exec();  
		    
		    String str7 = "INSERT INTO GRAPH <http://147.27.60.65/sensorOntology4> { <http://example.org/data/Home"+i+"LuminocitySensor> <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> <http://www.w3.org/2002/07/owl#NamedIndividual> }";
		    VirtuosoUpdateRequest vur7 = VirtuosoUpdateFactory.create(str7, vg);
		    vur7.exec();  
		    
		    String str8 = "INSERT INTO GRAPH <http://147.27.60.65/sensorOntology4> { <http://example.org/data/Home"+i+"HumiditySensor> <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> <http://www.w3.org/2002/07/owl#NamedIndividual> }";
		    VirtuosoUpdateRequest vur8 = VirtuosoUpdateFactory.create(str8, vg);
		    vur8.exec();  
		    
		    String str9 = "INSERT INTO GRAPH <http://147.27.60.65/sensorOntology4> { <http://example.org/data/Home"+i+"TemperatureSensor> <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> <http://www.w3.org/2002/07/owl#NamedIndividual> }";
		    VirtuosoUpdateRequest vur9 = VirtuosoUpdateFactory.create(str9, vg);
		    vur9.exec();  
		    
		    //random values
		    int value1 = 26;		
			int value2 = 81;
			int value3 = 25;

			String str10 = "INSERT INTO GRAPH <http://147.27.60.65/sensorOntology4> { <http://example.org/data/Home"+i+"LuminocityObservation> <http://www.w3.org/ns/sosa/hasSimpleResult> "+value1+" }";
		    VirtuosoUpdateRequest vur10 = VirtuosoUpdateFactory.create(str10, vg);
		    vur10.exec();  
		    
		    String str11 = "INSERT INTO GRAPH <http://147.27.60.65/sensorOntology4> { <http://example.org/data/Home"+i+"HumidityObservation> <http://www.w3.org/ns/sosa/hasSimpleResult> "+value2+" }";
		    VirtuosoUpdateRequest vur11 = VirtuosoUpdateFactory.create(str11, vg);
		    vur11.exec();  
		    
		    String str12 = "INSERT INTO GRAPH <http://147.27.60.65/sensorOntology4> { <http://example.org/data/Home"+i+"TemperatureObservation> <http://www.w3.org/ns/sosa/hasSimpleResult> "+value3+" }";
		    VirtuosoUpdateRequest vur12 = VirtuosoUpdateFactory.create(str12, vg);
		    vur12.exec();  
		    
		    String str13 = "INSERT INTO GRAPH <http://147.27.60.65/sensorOntology4> { <http://example.org/data/Home"+i+"LuminocitySensor> <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> <http://www.w3.org/ns/sosa/LuminocitySensor> }";
		    VirtuosoUpdateRequest vur13 = VirtuosoUpdateFactory.create(str13, vg);
		    vur13.exec();  
		    
		    String str14 = "INSERT INTO GRAPH <http://147.27.60.65/sensorOntology4> { <http://example.org/data/Home"+i+"HumiditySensor> <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> <http://www.w3.org/ns/sosa/HumiditySensor> }";
		    VirtuosoUpdateRequest vur14 = VirtuosoUpdateFactory.create(str14, vg);
		    vur14.exec(); 
		    
		    String str15 = "INSERT INTO GRAPH <http://147.27.60.65/sensorOntology4> { <http://example.org/data/Home"+i+"TempewratureSensor> <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> <http://www.w3.org/ns/sosa/TemperatureSensor> }";
		    VirtuosoUpdateRequest vur15 = VirtuosoUpdateFactory.create(str15, vg);
		    vur15.exec(); 
		    
		    String str16 = "INSERT INTO GRAPH <http://147.27.60.65/sensorOntology4> { <http://example.org/data/Home"+i+"LuminocitySensor> <http://www.w3.org/ns/sosa/madeObservation> <http://example.org/data/Home"+i+"LuminocityObservation> }";
		    VirtuosoUpdateRequest vur16 = VirtuosoUpdateFactory.create(str16, vg);
		    vur16.exec(); 
		    
		    String str17 = "INSERT INTO GRAPH <http://147.27.60.65/sensorOntology4> { <http://example.org/data/Home"+i+"TemperatureSensor> <http://www.w3.org/ns/sosa/madeObservation> <http://example.org/data/Home"+i+"TemperatureObservation> }";
		    VirtuosoUpdateRequest vur17 = VirtuosoUpdateFactory.create(str17, vg);
		    vur17.exec(); 
		    
		    String str18 = "INSERT INTO GRAPH <http://147.27.60.65/sensorOntology4> { <http://example.org/data/Home"+i+"HumiditySensor> <http://www.w3.org/ns/sosa/madeObservation> <http://example.org/data/Home"+i+"HumidityObservation> }";
		    VirtuosoUpdateRequest vur18 = VirtuosoUpdateFactory.create(str18, vg);
		    vur18.exec(); 
		    
		    String str19 = "INSERT INTO GRAPH <http://147.27.60.65/sensorOntology4> { <http://example.org/data/Home"+i+"Humidity> <http://www.w3.org/ns/sosa/isObservedBy> <http://example.org/data/Home"+i+"HumiditySensor> }";
		    VirtuosoUpdateRequest vur19 = VirtuosoUpdateFactory.create(str19, vg);
		    vur19.exec(); 
		    
		    String str20 = "INSERT INTO GRAPH <http://147.27.60.65/sensorOntology4> { <http://example.org/data/Home"+i+"Temperature> <http://www.w3.org/ns/sosa/isObservedBy> <http://example.org/data/Home"+i+"TemperatureSensor> }";
		    VirtuosoUpdateRequest vur20 = VirtuosoUpdateFactory.create(str20, vg);
		    vur20.exec();
		    
		    String str21 = "INSERT INTO GRAPH <http://147.27.60.65/sensorOntology4> { <http://example.org/data/Home"+i+"Luminocity> <http://www.w3.org/ns/sosa/isObservedBy> <http://example.org/data/Home"+i+"LuminocitySensor> }";
		    VirtuosoUpdateRequest vur21 = VirtuosoUpdateFactory.create(str21, vg);
		    vur21.exec();		
			
		    String str22 = "INSERT INTO GRAPH <http://147.27.60.65/sensorOntology4> { <http://example.org/data/Home"+i+"LuminocityObservation> <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> <http://www.w3.org/2002/07/owl#NamedIndividual> }";
		    VirtuosoUpdateRequest vur22 = VirtuosoUpdateFactory.create(str22, vg);
		    vur22.exec();  
		    
		    String str23 = "INSERT INTO GRAPH <http://147.27.60.65/sensorOntology4> { <http://example.org/data/Home"+i+"HumidityObservation> <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> <http://www.w3.org/2002/07/owl#NamedIndividual> }";
		    VirtuosoUpdateRequest vur23 = VirtuosoUpdateFactory.create(str23, vg);
		    vur23.exec();  
		    
		    String str24 = "INSERT INTO GRAPH <http://147.27.60.65/sensorOntology4> { <http://example.org/data/Home"+i+"TemperatureObservation> <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> <http://www.w3.org/2002/07/owl#NamedIndividual> }";
		    VirtuosoUpdateRequest vur24 = VirtuosoUpdateFactory.create(str24, vg);
		    vur24.exec();

		}
	}
	
	@GetMapping("/ask")
	@ResponseBody
	public boolean askquery(@RequestParam String thequery) throws UnirestException {
	

		Reasoner reasoner = PelletReasonerFactory.theInstance().create();
    		VirtGraph vg = new VirtGraph("http://147.27.60.65/sensorOntology", "jdbc:virtuoso://147.27.60.65:1111", "dba", "boto");
    		Query sparql = QueryFactory.create("PREFIX sosa: <http://www.w3.org/ns/sosa/>\r\n" + 
    		 		"PREFIX owl: <http://www.w3.org/2002/07/owl#>\r\n" + 
    		 		"PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>" +
    		 		"CONSTRUCT FROM <http://147.27.60.65/sensorOntology> WHERE {?s ?p ?o}");
        
        
    		QueryExecution vqe = VirtuosoQueryExecutionFactory.create (sparql, vg);

    		Model model1 = vqe.execConstruct();
 	        Graph g = model1.getGraph();
 	        
 	        
	        InfModel infModel = ModelFactory.createInfModel( reasoner, model1 );
	        Query sparql1 = QueryFactory.create(thequery);
	        QueryExecution qe = SparqlDLExecutionFactory.create(sparql1, infModel);
	        boolean b = qe.execAsk();
    		
    		qe.close();
			return b;
//		    return jsonResult;
		
	}
	
	
	@PostMapping("/updateactuations")
	@ResponseBody
	public void updateActuations() throws UnirestException {
				
		VirtGraph vg = new VirtGraph("http://147.27.60.65/sensorOntology", "jdbc:virtuoso://147.27.60.65:1111", "dba", "boto");
		Reasoner reasoner = PelletReasonerFactory.theInstance().create();
		Query sparql = QueryFactory.create("CONSTRUCT FROM <http://147.27.60.65/sensorOntology> WHERE {?s ?p ?o}");
    
    
		QueryExecution vqe = VirtuosoQueryExecutionFactory.create (sparql, vg);
		Model model = vqe.execConstruct();
	    InfModel infModel = ModelFactory.createInfModel( reasoner, model );
	    
	    ValidityReport validity = infModel.validate();
	    if(validity.isValid()) {
	    	System.out.println("Output of validation test no errors");
	    } else {
	    	System.out.println("Conflicts ");
	    	for(java.util.Iterator<Report> i = validity.getReports(); i.hasNext();) {
	    		ValidityReport.Report report = (ValidityReport.Report)i.next();
	    		System.out.println("."+report);
	    	}
	    }
	    
	    
		HttpResponse<JsonNode> jsonResponse1 = Unirest.get("http://147.27.60.182:1026/v2/entities?type=Actuation&limit=200")
				  .asJson();
		
		JSONArray results = jsonResponse1.getBody().getArray();
		//JSONArray results = myObj.;
		
		String[] ids = new String[results.length()];    
		
		for(int i=0;i<results.length();i++)
		{
		    JSONObject jsonObject = results.getJSONObject(i);
		    //System.out.println(jsonObject.getString("id"));
		    ids[i] = jsonObject.getString("id");
		    com.mashape.unirest.http.HttpResponse<JsonNode> jsonResponse = Unirest.post("http://147.27.60.182:1026/v2/entities/"+ids[i]+"/attrs")
					  .header("Content-Type", "application/json")
//					  .body("{\n    \"actuationEnabled\": {\n        "
//					  		+ "\"value\": {\n            "
//					  		+ "\"type\": \"Property\",\n            "
//					  		+ "\"value\": \"0\",\n            "
//					  		+ "\"context\": \"http://www.w3.org/ns/sosa/actuationEnabled\"\n        }\n    }\n}")
					  .body("{\n    \"actuationEnabled\": {\n        \"type\": \"Number\",\n        \"value\": 0,\n        \"metadata\": {}\n    }\n}")
					  .asJson();
			
			System.out.println(jsonResponse.getBody());
		}
		
		
		System.out.println("done init");
		//Resource res = infModel.getResource("http://example.org/data/"+ids[i]);
	    Property prop = infModel.getProperty("http://www.w3.org/ns/sosa/", "actuationEnabled");
	    ResIterator it = infModel.listResourcesWithProperty(prop);
	    System.out.println("here");
	    while (it.hasNext()) {
	    	Resource stmt = it.nextResource();
			System.out.println(stmt.toString());
			
			String s = stmt.toString();
			String rs = stmt.getLocalName();
			for(int i=0;i<results.length();i++) {
			    if(ids[i].equals(rs)) {
			    	com.mashape.unirest.http.HttpResponse<JsonNode> jsonResponse = Unirest.put("http://147.27.60.182:1026/v2/entities/"+ids[i]+"/attrs/actuationEnabled")
							  .header("Content-Type", "application/json")
//							  .body("{\n    \"type\": \"Property\",\n    "
//							  		+ "\"value\": \"1\",\n    "
//							  		+ "\"context\": \"http://www.w3.org/ns/sosa/actuationEnabled\"\n}")
							  .body("{\n    \"value\": 1\n}")
							  .asJson();
					
					System.out.println(jsonResponse.getBody());
			    }
			}
		    
		    
		}
	    
		
	}
	
	@PostMapping("/update")
	@ResponseBody
	public void updateHistory(@RequestBody String response) throws UnirestException, JSONException, ParseException {
		
		SimpleDateFormat simpleDateFormat = new SimpleDateFormat("yyyy-MM-dd'T'HH:mm:ss'Z'");
		
		String id = new String();
		int value = 0;
		Date date;
		Calendar cal = Calendar.getInstance();
		XSDDateTime txsd = new XSDDateTime(cal);
		JSONObject obj = new JSONObject(response);
		System.out.println(obj.getJSONArray("data"));
		for (int i = 0; i < obj.getJSONArray("data").length(); i++) {
            id = obj.getJSONArray("data").getJSONObject(i).getString("id");
            value = obj.getJSONArray("data").getJSONObject(i).getJSONObject("hasSimpleResult").getInt("value");
            date = simpleDateFormat.parse(obj.getJSONArray("data").getJSONObject(i).getJSONObject("resultTime").getJSONObject("value").getString("value").toString());
    		cal.setTime(date);
    		txsd = new XSDDateTime(cal);
    		System.out.println(txsd);
            System.out.println(id + value+ date);
        }
//		Date date = simpleDateFormat.parse(obj.getString("time").toString());
		
		VirtGraph vg = new VirtGraph("http://147.27.60.65/sensorOntology", "jdbc:virtuoso://147.27.60.65:1111", "dba", "boto");
		
	   
	    
//	    Resource s = model.getResource("http://example.org/data/Room145TemperatureObservation");
//	    System.out.println(model.containsResource(s));
//	    Property p = model.getProperty("http://www.w3.org/ns/sosa/hasSimpleResult");
//	    RDFNode o = model.createTypedLiteral(obj.getLong("value"));
//	    StmtIterator  it = model.listStatements(new SimpleSelector(s, p, (RDFNode) null));
//		while (it.hasNext()) {
//	    	
//			Statement stmt = it.nextStatement();
//			System.out.println(stmt.getSubject()+"> <"+stmt.getPredicate()+"> <"+stmt.getObject() + o);
//			model.remove(it);
//		}
//		 System.out.println("hello");
//		 Statement stmt1 = model.createStatement(s, p, o);
//		model.add(stmt1);
//		StmtIterator  it1 = model.listStatements(new SimpleSelector(s, p, (RDFNode) null));
//		while (it1.hasNext()) {
//	    	
//			Statement stmt = it1.nextStatement();
//			System.out.println(stmt.getSubject()+"> <"+stmt.getPredicate()+"> <"+stmt.getObject());
//		}
//		 System.out.println("hello");
		

		
		
		String str = "WITH <http://147.27.60.65/sensorOntology>\r\n" + 
				"DELETE {<http://example.org/data/"+id+"> <http://www.w3.org/ns/sosa/hasSimpleResult> ?o} \r\n" + 
				"INSERT {<http://example.org/data/"+id+"> <http://www.w3.org/ns/sosa/hasSimpleResult> "+value+"} "
				+ "WHERE \r\n" + 
				"  {<http://example.org/data/"+id+"> <http://www.w3.org/ns/sosa/hasSimpleResult> ?o} \r\n";
		VirtuosoUpdateRequest vur = VirtuosoUpdateFactory.create(str, vg);
		vur.exec();
		
		str = "WITH <http://147.27.60.65/sensorOntology>\r\n" + 
				"DELETE {<http://example.org/data/"+id+"> <http://www.w3.org/ns/sosa/resultTime> ?o} \r\n" + 
				"INSERT {<http://example.org/data/"+id+"> <http://www.w3.org/ns/sosa/resultTime> '"+txsd+"'^^xsd:dateTime} "
				+ "WHERE \r\n" + 
				"  {<http://example.org/data/"+id+"> <http://www.w3.org/ns/sosa/resultTime> ?o} \r\n";
		vur = VirtuosoUpdateFactory.create(str, vg);
		vur.exec();
		 
		
		VirtGraph vg1 = new VirtGraph("http://147.27.60.65/sensorOntology", "jdbc:virtuoso://147.27.60.65:1111", "dba", "boto");

	    Reasoner reasoner = PelletReasonerFactory.theInstance().create();
		Query sparql = QueryFactory.create("CONSTRUCT FROM <http://147.27.60.65/sensorOntology> WHERE {?s ?p ?o}");
    
    
		QueryExecution vqe = VirtuosoQueryExecutionFactory.create (sparql, vg1);
		Model model = vqe.execConstruct();
	    InfModel infModel = ModelFactory.createInfModel( reasoner, model );
	    
	    ValidityReport validity = infModel.validate();
	    if(validity.isValid()) {
	    	System.out.println("Output of validation test no errors");
	    } else {
	    	System.out.println("Conflicts ");
	    	for(java.util.Iterator<Report> i = validity.getReports(); i.hasNext();) {
	    		ValidityReport.Report report = (ValidityReport.Report)i.next();
	    		System.out.println("."+report);
	    	}
	    }
	    
	    updateActuations();
	    
	}
	
	@GetMapping("/history")
	@ResponseBody
	public void history() {
		long startTime = System.nanoTime();
		VirtGraph vg = new VirtGraph("http://147.27.60.65/sensorOntologyReasoner", "jdbc:virtuoso://147.27.60.65:1111", "dba", "boto");
		Query sparql = QueryFactory.create("PREFIX sosa: <http://www.w3.org/ns/sosa/>		 \r\n" + 
				"PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>\r\n" + 
				"SELECT ?s ?p ?o FROM <http://147.27.60.65/sensorOntologyReasoner> WHERE {?s ?p ?o.\r\n" + 
				"?s rdf:type sosa:Observation}");
    
    
		 VirtuosoQueryExecution vqe = VirtuosoQueryExecutionFactory.create (sparql, vg);
		 ResultSet results = vqe.execSelect();
		 
		 String str = "CLEAR GRAPH <http://147.27.60.65/sensorOntologyHistory>";
		 VirtuosoUpdateRequest vur = VirtuosoUpdateFactory.create(str, vg);
		 vur.exec();
		    
		    
			while (results.hasNext()) {
				QuerySolution result2 = results.nextSolution();
			    RDFNode s = result2.get("s");
			    RDFNode p = result2.get("p");
			    RDFNode o = result2.get("o");
			    
			    
			    System.out.println(" { " + s + " " + p + " " + o + "}");
			    if(o.isLiteral()) {
			    	str = "INSERT INTO GRAPH <http://147.27.60.65/sensorOntologyHistory> { <"+s.toString()+"> <"+p.toString()+"> '"+o.toString()+"'}";
					vur = VirtuosoUpdateFactory.create(str, vg);
			        vur.exec();
			    } else if(o.isResource()) {
			    	str = "INSERT INTO GRAPH <http://147.27.60.65/sensorOntologyHistory> { <"+s.toString()+"> <"+p.toString()+"> <"+o.asResource()+">}";
					vur = VirtuosoUpdateFactory.create(str, vg);
			        vur.exec();
			    }
			    
			}
	}
	
	@GetMapping("/reason")
	@ResponseBody
	public void reason() {
		long startTime = System.nanoTime();
		Reasoner reasoner = PelletReasonerFactory.theInstance().create();
		VirtGraph vg = new VirtGraph("http://147.27.60.65/sensorOntology", "jdbc:virtuoso://147.27.60.65:1111", "dba", "boto");
		Query sparql = QueryFactory.create("CONSTRUCT FROM <http://147.27.60.65/sensorOntology> WHERE {?s ?p ?o}");
    
    
		QueryExecution vqe = VirtuosoQueryExecutionFactory.create (sparql, vg);
		Model model = vqe.execConstruct();
	    InfModel infModel = ModelFactory.createInfModel( reasoner, model );
	    
	    String str = "CLEAR GRAPH <http://147.27.60.65/sensorOntologyReasoner>";
	    VirtuosoUpdateRequest vur = VirtuosoUpdateFactory.create(str, vg);
	    vur.exec();
	    
	    Resource r = infModel.getResource("http://www.w3.org/ns/sosa/Observation");
	    help(infModel, r, vg);
	    r = infModel.getResource("http://www.w3.org/ns/sosa/Actuation");
	    help(infModel, r, vg);
	    r = infModel.getResource("http://www.w3.org/ns/sosa/Sensor");
	    help(infModel, r, vg);
	    r = infModel.getResource("http://www.w3.org/ns/sosa/ObservableProperty");
	    help(infModel, r, vg);
	    r = infModel.getResource("http://www.w3.org/ns/sosa/Home");
	    help(infModel, r, vg);
	    r = infModel.getResource("http://www.w3.org/2003/01/geo/wgs84_pos#SpatialThing");
	    help(infModel, r, vg);

	    long endTime = System.nanoTime();
	    long duration = (endTime - startTime)/1000000;
	    System.out.println(duration);
	}
	
	public void help(InfModel infModel, Resource r, VirtGraph vg) {
		ResIterator it = infModel.listResourcesWithProperty(RDF.type, r);
	    while (it.hasNext()) {
	    	Resource stmt1 = it.nextResource();
	    	StmtIterator it1 = infModel.listStatements(stmt1, null, (RDFNode) null);
	    	while (it1.hasNext()) {
		    	Statement stmt = it1.nextStatement();
		    	//System.out.println(stmt);
		    	if (!stmt.getPredicate().asResource().getURI().startsWith("http://www.w3.org/2002/07/owl#")
						&& !stmt.getPredicate().asResource().getURI().startsWith("http://www.w3.org/1999/02/22-rdf-syntax-ns#type")
						&& stmt.getObject().isURIResource()
						&& stmt.getSubject().getURI().startsWith("http://example.org/data/")) {
					//System.out.println(stmt.getSubject()+" "+stmt.getPredicate()+" "+stmt.getObject());
		    		String str = "INSERT INTO GRAPH <http://147.27.60.65/sensorOntologyReasoner> { <"+stmt.getSubject()+"> <"+stmt.getPredicate()+"> <"+stmt.getObject()+">}";
		    	    VirtuosoUpdateRequest vur = VirtuosoUpdateFactory.create(str, vg);
			        vur.exec();
					
				} else if(!stmt.getPredicate().asResource().getURI().startsWith("http://www.w3.org/2002/07/owl#")
						&& !stmt.getPredicate().asResource().getURI().startsWith("http://www.w3.org/1999/02/22-rdf-syntax-ns#type")
						&& !stmt.getObject().isURIResource()
						&& stmt.getSubject().getURI().startsWith("http://example.org/data/")) {
					//System.out.println(stmt.getSubject()+" "+stmt.getPredicate()+" "+stmt.getObject());
					String str = "INSERT INTO GRAPH <http://147.27.60.65/sensorOntologyReasoner> { <"+stmt.getSubject()+"> <"+stmt.getPredicate()+"> '"+stmt.getObject().asLiteral()+"'}";
					VirtuosoUpdateRequest vur = VirtuosoUpdateFactory.create(str, vg);
			        vur.exec();
				} else if(!stmt.getPredicate().asResource().getURI().startsWith("http://www.w3.org/2002/07/owl#")
						&& stmt.getPredicate().asResource().getURI().startsWith("http://www.w3.org/1999/02/22-rdf-syntax-ns#type")
						&& !stmt.getObject().asResource().getURI().startsWith("http://www.w3.org/2002/07/owl#")
						&& stmt.getSubject().getURI().startsWith("http://example.org/data/")) {
					//System.out.println(stmt.getSubject()+" "+stmt.getPredicate()+" "+stmt.getObject());
					String str = "INSERT INTO GRAPH <http://147.27.60.65/sensorOntologyReasoner> { <"+stmt.getSubject()+"> <"+stmt.getPredicate()+"> <"+stmt.getObject()+">}";
					VirtuosoUpdateRequest vur = VirtuosoUpdateFactory.create(str, vg);
			        vur.exec();
				}
	    	}
	    }
		
	}
	
	
	@GetMapping("/reasoner")
	@ResponseBody
	public void reasoner() {
		long startTime = System.nanoTime();
		Reasoner reasoner = PelletReasonerFactory.theInstance().create();
		VirtGraph vg = new VirtGraph("http://147.27.60.65/sensorOntology", "jdbc:virtuoso://147.27.60.65:1111", "dba", "boto");
		Query sparql = QueryFactory.create("CONSTRUCT FROM <http://147.27.60.65/sensorOntology> WHERE {?s ?p ?o}");
    
    
		QueryExecution vqe = VirtuosoQueryExecutionFactory.create (sparql, vg);
		Model model = vqe.execConstruct();
	    InfModel infModel = ModelFactory.createInfModel( reasoner, model );
	    
	    String str = "CLEAR GRAPH <http://147.27.60.65/sensorOntologyReasoner>";
	    VirtuosoUpdateRequest vur = VirtuosoUpdateFactory.create(str, vg);
	    vur.exec();
	    
	    StmtIterator it = infModel.listStatements();
	    while (it.hasNext()) {
	    	Statement stmt = it.nextStatement();
			if (stmt.getSubject().isURIResource()
					&& !stmt.getPredicate().asResource().getURI().startsWith("http://www.w3.org/2002/07/owl#")
					&& !stmt.getPredicate().asResource().getURI().startsWith("http://www.w3.org/1999/02/22-rdf-syntax-ns#type")
					&& stmt.getObject().isURIResource()
					&& stmt.getSubject().getURI().startsWith("http://example.org/data/")) {
				//System.out.println(stmt.getSubject()+" "+stmt.getPredicate()+" "+stmt.getObject());
				str = "INSERT INTO GRAPH <http://147.27.60.65/sensorOntologyReasoner> { <"+stmt.getSubject()+"> <"+stmt.getPredicate()+"> <"+stmt.getObject()+">}";
				vur = VirtuosoUpdateFactory.create(str, vg);
		        vur.exec();
				
			} else if(stmt.getSubject().isURIResource()
					&& !stmt.getPredicate().asResource().getURI().startsWith("http://www.w3.org/2002/07/owl#")
					&& !stmt.getPredicate().asResource().getURI().startsWith("http://www.w3.org/1999/02/22-rdf-syntax-ns#type")
					&& !stmt.getObject().isURIResource()
					&& stmt.getSubject().getURI().startsWith("http://example.org/data/")) {
				//System.out.println(stmt.getSubject()+" "+stmt.getPredicate()+" "+stmt.getObject());
				str = "INSERT INTO GRAPH <http://147.27.60.65/sensorOntologyReasoner> { <"+stmt.getSubject()+"> <"+stmt.getPredicate()+"> '"+stmt.getObject().asLiteral()+"'}";
				vur = VirtuosoUpdateFactory.create(str, vg);
		        vur.exec();
			} else if(stmt.getSubject().isURIResource()
					&& !stmt.getPredicate().asResource().getURI().startsWith("http://www.w3.org/2002/07/owl#")
					&& stmt.getPredicate().asResource().getURI().startsWith("http://www.w3.org/1999/02/22-rdf-syntax-ns#type")
					&& !stmt.getObject().asResource().getURI().startsWith("http://www.w3.org/2002/07/owl#")
					&& stmt.getSubject().getURI().startsWith("http://example.org/data/")) {
				//System.out.println(stmt.getSubject()+" "+stmt.getPredicate()+" "+stmt.getObject());
				str = "INSERT INTO GRAPH <http://147.27.60.65/sensorOntologyReasoner> { <"+stmt.getSubject()+"> <"+stmt.getPredicate()+"> <"+stmt.getObject()+">}";
				vur = VirtuosoUpdateFactory.create(str, vg);
		        vur.exec();
			}
	    }
	    long endTime = System.nanoTime();
	    long duration = (endTime - startTime)/1000000;
	    System.out.println(duration);
	}
	
	@GetMapping("/insertdata")
	@ResponseBody
	public void insertDataToCB() throws UnirestException {
		VirtGraph vg = new VirtGraph("http://147.27.60.65/sensorOntologyReasoner", "jdbc:virtuoso://147.27.60.65:1111", "dba", "boto");
		Query sparql = QueryFactory.create("PREFIX sosa: <http://www.w3.org/ns/sosa/>\r\n" + 
		 		"PREFIX owl: <http://www.w3.org/2002/07/owl#>\r\n" + 
		 		"PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>" +
		 		"CONSTRUCT FROM <http://147.27.60.65/sensorOntologyReasoner> WHERE {?s ?p ?o}");
    
    
		QueryExecution vqe = VirtuosoQueryExecutionFactory.create (sparql, vg);
		Model model = vqe.execConstruct();
		
	    StmtIterator it = model.listStatements();
	    while (it.hasNext()) {
			Statement stmt = it.nextStatement();
			if (stmt.getSubject().isURIResource()
					&& stmt.getSubject().getURI().startsWith("http://example.org/data/")
					&& stmt.getPredicate().isURIResource()
					&& stmt.getObject().isURIResource()
					&& stmt.getPredicate().asResource().getURI().startsWith("http://www.w3.org/1999/02/22-rdf-syntax-ns#type")
					//&& !stmt.getObject().asResource().getURI().startsWith("http://www.w3.org/ns/sosa/ObservableProperty")
					&& (stmt.getObject().asResource().getURI().startsWith("http://www.w3.org/ns/sosa/Sensor")
							|| stmt.getObject().asResource().getURI().startsWith("http://www.w3.org/ns/sosa/Observation")
							|| stmt.getObject().asResource().getURI().startsWith("http://www.w3.org/ns/sosa/Actuation")
							|| stmt.getObject().asResource().getURI().startsWith("http://www.w3.org/ns/sosa/ObservableProperty")
							|| stmt.getObject().asResource().getURI().startsWith("http://www.w3.org/ns/sosa/Home")
							|| stmt.getObject().asResource().getURI().startsWith("http://www.w3.org/2003/01/geo/wgs84_pos#SpatialThing"))
					&& !stmt.getObject().asResource().getURI().startsWith("http://www.w3.org/2002/07/owl#")) {
				//System.out.println(stmt.getSubject()+" "+stmt.getPredicate()+" "+stmt.getObject());
				
				String s = stmt.getSubject().toString();
				String rs = stmt.getSubject().getLocalName();
			    //System.out.println(rs);
			    String rc = stmt.getObject().asNode().getLocalName();
			    //System.out.println(rc);

				
				StmtIterator it1 = model.listStatements();
				while (it1.hasNext()) {
					Statement stmt1 = it1.nextStatement();
					if (stmt1.getSubject().toString().equals(stmt.getSubject().toString())
							&& stmt1.getSubject().getURI().startsWith("http://example.org/data/")
							&& stmt1.getPredicate().isURIResource()
							&& !stmt1.getObject().isURIResource()
							&& !stmt1.getPredicate().asResource().getURI().startsWith("http://www.w3.org/2002/07/owl#")
							&& !stmt1.getPredicate().asResource().getURI().startsWith("http://www.w3.org/2000/01/rdf-schema#comment")
							&& !stmt1.getPredicate().asResource().getURI().startsWith("http://www.w3.org/2000/01/rdf-schema#label")) {
						System.out.println(stmt1.getSubject()+" "+stmt1.getPredicate()+" "+stmt1.getObject().asLiteral().getString()/*stmt1.getObject().asLiteral().getString().substring(0, stmt1.getObject().asLiteral().getString().indexOf("^"))*/);
						

						s = stmt1.getSubject().toString();
						rs = stmt1.getSubject().getLocalName();
					    //System.out.println(rs);
					    String p = stmt1.getPredicate().toString();
					    String rp = stmt1.getPredicate().getLocalName();
					    String rp1 = stmt1.getPredicate().toString();
					    rp1 = rp1.substring(0, rp1.lastIndexOf("/"));
					    //System.out.println(rp);
					    //System.out.println(rp1);
					    String o = stmt1.getObject().asLiteral().getString().substring(0, stmt1.getObject().asLiteral().getString().indexOf("^"));
					    //String ro = stmt1.getObject().asNode().getLocalName();
					    //System.out.println(ro);
					    
					    if(stmt1.getObject().asLiteral().getString().contains("date")) {
					    	com.mashape.unirest.http.HttpResponse<JsonNode> jsonResponse1 = Unirest.post("http://147.27.60.182:1026/v2/entities/"+rs+"/attrs?type="+rc+"&options=keyValues")
									  .header("Content-Type", "application/json")
									  .body("{\n\t\""+rp+"\": {\n        "
									  		+ "\"type\": \"DateTime\",\n        "
									  		+ "\"value\": \""+o+"\"\n    }\n}")
									  .asJson();
							
							System.out.println(jsonResponse1.getBody());
						} else {
							com.mashape.unirest.http.HttpResponse<JsonNode> jsonResponse1 = Unirest.post("http://147.27.60.182:1026/v2/entities/"+rs+"/attrs?type="+rc+"&options=keyValues")
									  .header("Content-Type", "application/json")
//									  .body("{\n\t\""+rp+"\": {\n        "
//									  		+ "\"type\": \"Property\",\n        "
//									  		+ "\"value\": \""+o+"\",\n"
//									  		+ "\"context\": \""+p+"\"\n    }\n}")
									  .body("{\n    \""+rp+"\": "+o+"\n}")
									  .asJson();
							
							System.out.println(jsonResponse1.getBody());
						}
					}
					
				}
				
			}
		}
	}
	@GetMapping("/insert")
	@ResponseBody
	public void insertToCB() throws UnirestException {
		VirtGraph vg = new VirtGraph("http://147.27.60.65/sensorOntologyReasoner", "jdbc:virtuoso://147.27.60.65:1111", "dba", "boto");
		Query sparql = QueryFactory.create("PREFIX sosa: <http://www.w3.org/ns/sosa/>\r\n" + 
		 		"PREFIX owl: <http://www.w3.org/2002/07/owl#>\r\n" + 
		 		"PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>" +
		 		"CONSTRUCT FROM <http://147.27.60.65/sensorOntologyReasoner> WHERE {?s ?p ?o}");
    
    
		QueryExecution vqe = VirtuosoQueryExecutionFactory.create (sparql, vg);
		Model model = vqe.execConstruct();
	
	    
	    //to send obj prop to cb
	    StmtIterator it = model.listStatements();
	    while (it.hasNext()) {
			Statement stmt = it.nextStatement();
			if (stmt.getSubject().isURIResource()
					&& stmt.getSubject().getURI().startsWith("http://example.org/data/")
					&& stmt.getPredicate().isURIResource()
					&& stmt.getObject().isURIResource()
					&& stmt.getPredicate().asResource().getURI().startsWith("http://www.w3.org/1999/02/22-rdf-syntax-ns#type")
					//&& !stmt.getObject().asResource().getURI().startsWith("http://www.w3.org/ns/sosa/ObservableProperty")
					&& (stmt.getObject().asResource().getURI().startsWith("http://www.w3.org/ns/sosa/Home")
							|| stmt.getObject().asResource().getURI().startsWith("http://www.w3.org/ns/sosa/Observation")
							|| stmt.getObject().asResource().getURI().startsWith("http://www.w3.org/ns/sosa/Actuation")
							|| stmt.getObject().asResource().getURI().startsWith("http://www.w3.org/2003/01/geo/wgs84_pos#SpatialThing")
							|| stmt.getObject().asResource().getURI().startsWith("http://www.w3.org/ns/sosa/ObservableProperty")
							|| stmt.getObject().asResource().getURI().startsWith("http://www.w3.org/ns/sosa/Sensor"))
					&& !stmt.getObject().asResource().getURI().startsWith("http://www.w3.org/2002/07/owl#")) {
				//System.out.println(stmt.getSubject()+" "+stmt.getPredicate()+" "+stmt.getObject());
				
				String s = stmt.getSubject().toString();
				String rs = stmt.getSubject().getLocalName();
			    //System.out.println(rs);
			    String rc = stmt.getObject().asNode().getLocalName();
			    //System.out.println(rc);
//				
				com.mashape.unirest.http.HttpResponse<JsonNode> jsonResponse = Unirest.post("http://147.27.60.182:1026/v2/entities?options=keyValues")
						  .header("Content-Type", "application/json")
						  .body("{\n    \"id\": \""+rs+"\",\n    "
						  		+ "\"type\": \""+rc+"\",\n    "
						  		+ "\"context\": \""+s+"\"\n}")
						  .asJson();
				
				System.out.println(jsonResponse.getBody());
				
				StmtIterator it1 = model.listStatements();
				while (it1.hasNext()) {
					Statement stmt1 = it1.nextStatement();
					if (stmt1.getSubject().toString().equals(stmt.getSubject().toString())
							&& stmt1.getSubject().getURI().startsWith("http://example.org/data/")
							&& stmt1.getPredicate().isURIResource()
							&& stmt1.getObject().isURIResource()
							&& !stmt1.getPredicate().asResource().getURI().startsWith("http://www.w3.org/2002/07/owl#")
							&& stmt1.getObject().asResource().getURI().startsWith("http://example.org/data/")) {
						//System.out.println(stmt1.getSubject()+" "+stmt1.getPredicate()+" "+stmt1.getObject());
						
						s = stmt1.getSubject().toString();
						rs = stmt1.getSubject().getLocalName();
					    //System.out.println(rs);
					    String p = stmt1.getPredicate().toString();
					    String rp = stmt1.getPredicate().getLocalName();
					    String rp1 = stmt1.getPredicate().toString();
					    rp1 = rp1.substring(0, rp1.lastIndexOf("/"));
					    //System.out.println(rp);
					    //System.out.println(rp1);
					    String o = stmt1.getObject().toString();
					    String ro = stmt1.getObject().asNode().getLocalName();
					    System.out.println(ro);
					    
						com.mashape.unirest.http.HttpResponse<JsonNode> jsonResponse1 = Unirest.post("http://147.27.60.182:1026/v2/entities/"+rs+"/attrs?type="+rc+"&options=keyValues")
								  .header("Content-Type", "application/json")
								  .body("{\n\t\""+rp+"\": {\n        "
								  		+ "\"type\": \"Relationship\",\n        "
								  		+ "\"object\": \""+o+"\",\n"
								  		+ "\"context\": \""+p+"\"\n    }\n}")
								  .asJson();
						
						System.out.println(jsonResponse1.getBody());
					}
					
				}
				
			}
		}		
	}
	
	public static void insertObjToCB(InfModel inf) throws UnirestException {
		VirtGraph vg = new VirtGraph("http://147.27.60.65/sensorOntology", "jdbc:virtuoso://147.27.60.65:1111", "dba", "boto");		
		 Query sparql = QueryFactory.create("PREFIX sosa: <http://www.w3.org/ns/sosa/>\r\n" + 
		 		"PREFIX owl: <http://www.w3.org/2002/07/owl#>\r\n" + 
		 		"PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>" +
		 		"PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>" +
		 		"SELECT ?s ?c ?p ?o FROM <http://147.27.60.65/sensorOntology> WHERE {?s rdf:type owl:NamedIndividual.\r\n" + 
		 		"?p rdf:type owl:ObjectProperty.\r\n" + 
		 		"?c rdf:type owl:Class.\r\n" + 
		 		"?s rdf:type ?c.\r\n" +
		 		"filter(?c != owl:Thing).\r\n" +
		 		"?s ?p ?o}");
		 QueryExecution vqe = QueryExecutionFactory.create(sparql, inf);
		 ResultSet results = vqe.execSelect();
			while (results.hasNext()) {
				QuerySolution result = results.nextSolution();
			    RDFNode s = result.get("s");
			    RDFNode p = result.get("p");
			    RDFNode o = result.get("o");
			    RDFNode c = result.get("c");
			    
			    if(s.isURIResource() && p.isURIResource() && o.isURIResource() && c.isURIResource()) {
			    
			    
				    System.out.println(" { " + s + " " + c + " " + p + " " + o + "}");
				    String rs = s.asNode().getLocalName();
				    System.out.println(rs);
				    String rc = c.asNode().getLocalName();
				    System.out.println(rc);
				    String rp = p.asNode().getLocalName();
				    String rp1 = p.asNode().toString();
				    rp1 = rp1.substring(0, rp1.lastIndexOf("/"));
				    System.out.println(rp);
				    System.out.println(rp1);
				    String ro = o.asNode().getLocalName();
				    System.out.println(ro);
				    
				    com.mashape.unirest.http.HttpResponse<JsonNode> jsonResponse = Unirest.post("http://147.27.60.182:1026/v2/entities?options=keyValues")
							  .header("Content-Type", "application/json")
							  .body("{\n    \"id\": \""+rs+"\",\n    "
							  		+ "\"type\": \""+rc+"\",\n    "
							  		+ "\"context\": \""+s+"\",\n    "
							  		+ "\""+rp+"\": {\r\n        "
							  		+ "\"type\": \"Relationship\",\r\n        "
							  		+ "\"object\": \""+o+"\",\n"
							  		+ "\"context\": \""+p+"\"\n    }\n}")
							  .asJson();
					
					System.out.println(jsonResponse.getBody());
					
					com.mashape.unirest.http.HttpResponse<JsonNode> jsonResponse1 = Unirest.post("http://147.27.60.182:1026/v2/entities/"+rs+"/attrs?type="+rc+"&options=keyValues")
							  .header("Content-Type", "application/json")
							  .body("{\n\t\""+rp+"\": {\n        "
							  		+ "\"type\": \"Relationship\",\n        "
							  		+ "\"object\": \""+o+"\",\n"
							  		+ "\"context\": \""+p+"\"\n    }\n}")
							  .asJson();
					
					System.out.println(jsonResponse1.getBody());
			    }
			    
			}
	}
	public static void insertDataToCB(InfModel inf) throws UnirestException {
		//VirtGraph vg = new VirtGraph("http://147.27.60.65/sensorOntology", "jdbc:virtuoso://147.27.60.65:1111", "dba", "boto");
		 Query sparql = QueryFactory.create("PREFIX sosa: <http://www.w3.org/ns/sosa/>\r\n PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#> PREFIX owl: <http://www.w3.org/2002/07/owl#>"  + 
		 		"PREFIX sosa: <http://www.w3.org/ns/sosa/>\r\n" + 
		 		"SELECT ?s ?c ?p ?o FROM <http://147.27.60.65/sensorOntology> WHERE {?p rdf:type owl:DatatypeProperty.\r\n" + 
		 		"?s rdf:type owl:NamedIndividual.\r\n" + 
		 		"?c rdf:type owl:Class.\r\n" + 
		 		"?s rdf:type ?c.\r\n" +
		 		"filter(?c != owl:Thing).\r\n" +
		 		"?s ?p ?o} limit 100");
		 QueryExecution vqe = SparqlDLExecutionFactory.create(sparql, inf);
		 ResultSet results = vqe.execSelect();
			while (results.hasNext()) {
				QuerySolution result = results.nextSolution();
			    RDFNode s = result.get("s");
			    RDFNode p = result.get("p");
			    RDFNode o = result.get("o");
			    RDFNode c = result.get("c");
			    System.out.println(" { " + s + " " + c + " " + p + " " + o + "}");
			    String rs = s.asNode().getLocalName();
			    System.out.println(rs);
			    String rc = c.asNode().getLocalName();
			    System.out.println(rc);
			    String rp = p.asNode().getLocalName();
			    System.out.println(rp);
			    //String ro = o.asNode().getLocalName();
			    //o.substring( 0, o.indexOf("^"));
			    System.out.println(o);
			    com.mashape.unirest.http.HttpResponse<JsonNode> jsonResponse = Unirest.post("http://147.27.60.182:1026/v2/entities?options=keyValues")
						  .header("Content-Type", "application/json")
						  .body("{\n    \"id\": \""+rs+"\",\n    "
						  		+ "\"type\": \""+rc+"\",\n    "
						  		+ "\"context\": \""+s+"\",\n    "
						  		+ "\""+rp+"\": {\r\n        "
						  		+ "\"type\": \"Property\",\r\n        "
						  		+ "\"value\": \""+o+"\",\n"
						  		+ "\"context\": \""+p+"\"\n    }\n}")
						  .asJson();
				
				System.out.println(jsonResponse.getBody());
				
				com.mashape.unirest.http.HttpResponse<JsonNode> jsonResponse1 = Unirest.post("http://147.27.60.182:1026/v2/entities/"+rs+"/attrs?options=keyValues")
						  .header("Content-Type", "application/json")
						  .body("{\n\t\""+rp+"\": {\n        "
								  + "\"type\": \"Property\",\r\n        "
							  		+ "\"value\": \""+o+"\",\n"
							  		+ "\"context\": \""+p+"\"\n    }\n}")
						  .asJson();
				
				System.out.println(jsonResponse1.getBody());
			}
	}

}
